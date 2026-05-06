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

// 2. LÓGICA DE REQUISIÇÕES
$error = '';

// Criar nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {

    // ✅ CORREÇÃO 1: capturar $descricao que estava faltando
    $title        = trim($_POST['title']);
    $descricao    = trim($_POST['descricao']);       // era: variável não existia
    $responsavel  = trim($_POST['responsavel']);     // era: $responsvel (typo)
    $dataVencimento = trim($_POST['dataVencimento']);

    if (empty($title)) {
        $error = "O título da tarefa não pode estar vazio!";
    } elseif (empty($responsavel)) {
        $error = "O responsável não pode estar vazio!";
    } elseif (empty($dataVencimento)) {
        $error = "A data de vencimento não pode estar vazia!";
    } else {
        // ✅ CORREÇÃO 2: removido o espaço em ': dataVencimento' → ':dataVencimento'
        $stmt = $pdo->prepare("INSERT INTO tasks (title, descricao, responsavel, dataVencimento)
            VALUES (:title, :descricao, :responsavel, :dataVencimento)");
        $stmt->bindValue(':title',          $title);
        $stmt->bindValue(':descricao',      $descricao);
        $stmt->bindValue(':responsavel',    $responsavel);  // era: $responsvel
        $stmt->bindValue(':dataVencimento', $dataVencimento);
        $stmt->execute();

        header("Location: index.php");
        exit;
    }
}

// Concluir ou excluir tarefa
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    if ($_GET['action'] === 'complete') {
        $pdo->exec("UPDATE tasks SET done = 1 WHERE id = $id");
    } elseif ($_GET['action'] === 'delete') {
        $pdo->exec("DELETE FROM tasks WHERE id = $id");
    }

    header("Location: index.php");
    exit;
}

// 3. BUSCA DE DADOS
$stmt  = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Master - Spaghetti</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            color: #333;
            display: flex;
            justify-content: center;
            padding-top: 50px;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }
        h1 {
            font-size: 1.5rem;
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .error {
            color: #dc2626;
            background: #fee2e2;
            padding: 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        /* MUDANÇA: form agora é em coluna, não em linha */
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        form input,
        form textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.95rem;
            font-family: inherit;
        }
        form textarea {
            resize: vertical;
            min-height: 70px;
        }
        form label {
            font-size: 0.85rem;
            color: #555;
            margin-bottom: 2px;
        }
        .field {
            display: flex;
            flex-direction: column;
        }
        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover { background: #1d4ed8; }

        ul { list-style: none; padding: 0; }

        /* MUDANÇA: li agora empilha as infos verticalmente */
        li {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 14px 0;
            border-bottom: 1px solid #eee;
            gap: 10px;
        }
        li.done .task-title {
            text-decoration: line-through;
            color: #9ca3af;
        }

        .task-info { flex: 1; }
        .task-title { font-weight: 600; margin-bottom: 4px; }
        .task-desc  { font-size: 0.88rem; color: #555; margin-bottom: 6px; }

        /* NOVO: linha de metadados (responsável + data) */
        .task-meta {
            display: flex;
            gap: 16px;
            font-size: 0.82rem;
            color: #6b7280;
        }
        .task-meta span { display: flex; align-items: center; gap: 4px; }

        .actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .actions a { text-decoration: none; cursor: pointer; font-size: 1.1rem; }
    </style>
</head>
<body>

<div class="container">
    <h1>Task Master (Spaghetti Edition)</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php">

        <div class="field">
            <label for="title">Título *</label>
            <input type="text" id="title" name="title"
                   placeholder="O que precisa ser feito?" autocomplete="off">
        </div>

        <!-- NOVO CAMPO: descrição -->
        <div class="field">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao"
                      placeholder="Detalhes sobre a tarefa (opcional)"></textarea>
        </div>

        <!-- NOVO CAMPO: responsável -->
        <div class="field">
            <label for="responsavel">Responsável *</label>
            <input type="text" id="responsavel" name="responsavel"
                   placeholder="Nome de quem vai executar">
        </div>

        <!-- NOVO CAMPO: data de vencimento -->
        <div class="field">
            <label for="dataVencimento">Data de vencimento *</label>
            <input type="date" id="dataVencimento" name="dataVencimento">
        </div>

        <button type="submit">Adicionar tarefa</button>
    </form>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?php echo $task['done'] ? 'done' : ''; ?>">

                <div class="task-info">
                    <!-- Título -->
                    <div class="task-title">
                        <?php echo htmlspecialchars($task['title']); ?>
                    </div>

                    <!-- Descrição (só exibe se tiver algo) -->
                    <?php if (!empty($task['descricao'])): ?>
                        <div class="task-desc">
                            <?php echo htmlspecialchars($task['descricao']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Responsável e data -->
                    <div class="task-meta">
                        <span>👤 <?php echo htmlspecialchars($task['responsavel']); ?></span>
                        <span>📅 <?php echo htmlspecialchars($task['dataVencimento']); ?></span>
                    </div>
                </div>

                <div class="actions">
                    <?php if (!$task['done']): ?>
                        <a href="?action=complete&id=<?php echo $task['id']; ?>"
                           title="Concluir">✅</a>
                    <?php endif; ?>
                    <a href="?action=delete&id=<?php echo $task['id']; ?>"
                       onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');"
                       title="Excluir">❌</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>