<?php
namespace Underline\Database;

/**
 * Executa uma função que é envolvida dentro de uma Transaction do PDO
 * @param \PDO $database
 * @param callable|string $function
 * @return void
 */
function doInTransaction(\PDO $database, callable | string $function) {
  try {
    $database->beginTransaction();
    $function();
    $database->commit();
  } catch (\PDOException $ex) {
    \Underline\Miscellaneous\echoExeption($ex);
    $database->rollBack();
  }
}

class Pagination {
  function __construct(
    private \PDO $database,
    private string $query,
    private int $page_size = 15
  ) {}

  function getPage(int $page, array $params = []) {
    $offset = $page * $this->page_size;
    $stmt = $this->database->prepare("$this->query LIMIT $this->page_size OFFSET $offset");

    if($stmt->execute($params)) {
      $data = $stmt->fetchAll(); 
      $stmt->closeCursor();
      return $data;
    }
    return null;
  }

  function getTotalOfPages(array $params = [], string $column = 'id') {
    $stmt = $this->database->prepare("SELECT COUNT($column) FROM ($this->query) sub");
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    $stmt->closeCursor();
    return ceil($total / $this->page_size);
  }
}