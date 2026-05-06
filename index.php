<?php
// ==========================================
// AULA 01: O CÓDIGO SPAGHETTI (Tudo misturado)
// ==========================================

// 1. CONEXÃO COM O BANCO DE DADOS
$dbFile = __DIR__ . '/tasks.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS tasks ( 
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    done INTEGER DEFAULT 0,
    descricao TEXT,
    responsavel VARCHAR NOT NULL,
    dataVencimento DATE NOT NULL
)");

// 2. LÓGICA DE NEGÓCIO E CONTROLE DE REQUISIÇÕES MISTURADOS
//
$error = '';

// Criar nova tarefa
//POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])
     && isset($_POST['dataVencimento']) 
    && isset($_POST['responsavel']))     {
    $title = trim($_POST['title']);
    $responsavel = trim($_POST['responsavel']);
    $dataVencimento = trim($_POST['dataVencimento']);

    if (empty($title)) {
        $error = "O título da tarefa não pode estar vazio!";
    }elseif(empty($responsavel)){
        $error = "O responsavel não pode estar vazio!";
    }
    $dataObj = DateTime::createFromFormat('Y-m-d', $dataVencimento);
    if (!$dataObj) {
    $error = "Data inválida!";
}
     else {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, descricao, responsavel, dataVencimento)
         VALUES (:title, :descricao, :responsavel, :dataVencimento )");
        $stmt->bindValue(':title', $title); //previnir sqlinjection
        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':responsavel', $responsavel);
        $stmt->bindValue(':dataVencimento', $dataVencimento);
        $stmt->execute();

        header("Location: index.php");
        exit;
    }
}

// Concluir ou excluir tarefa
//DELETE
if (isset($_GET['action'])
     && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
   
    
    if ($_GET['action'] === 'complete') {
       $stmt- $pdo->prepare("UPDATE tasks SET done = 1 WHERE id = $id");
       $stmt -> execute([$id]);
    } elseif ($_GET['action'] === 'delete') {
       $stmt- $pdo->prepare("DELETE FROM tasks WHERE id = $id");
       $stmt -> execute([$id]);
    }

    header("Location: index.php");
    exit;
}

// 3. BUSCA DE DADOS
$stmt  = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

