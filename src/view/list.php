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
    <h1>Task Master (MVC Edition)</h1>
   
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- O formulário agora aponta para a action 'create' -->
    <form method="POST" action="index.php?action=create" class="form-group">
        <input type="text" name="title" placeholder="Título" required>
        <input type="text" name="description" placeholder="Descrição">
        <input type="date" name="due_date" required>
        <button type="submit">Adicionar</button>
    </form>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?php echo $task['done'] ? 'done' : ''; ?>">
                <div>
                    <strong><?php echo htmlspecialchars($task['title']); ?></strong><br>
                    <small><?php echo htmlspecialchars($task['description']); ?> | Vence em: <?php echo $task['due_date']; ?></small>
                </div>
                <div class="actions">
                    <?php if (!$task['done']): ?>
                        <a href="index.php?action=complete&id=<?php echo $task['id']; ?>">✅</a>
                    <?php endif; ?>
                    <a href="index.php?action=delete&id=<?php echo $task['id']; ?>">❌</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>