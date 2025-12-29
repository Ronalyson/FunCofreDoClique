<?php
require __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
$action = $_GET['action'] ?? '';

try {
    if ($action === 'state') {
        echo json_encode(fetch_state($pdo));
    } elseif ($action === 'click' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(handle_click($pdo));
    } elseif ($action === 'withdraw' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(handle_withdraw($pdo));
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Ação inválida']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno']);
}

function fetch_state(PDO $pdo) {
    $state = $pdo->query("SELECT total_cents, total_clicks, updated_at FROM wallet_state WHERE id = 1")->fetch();
    $withdrawals = $pdo->query("SELECT name, reason, amount_cents, created_at FROM withdrawals ORDER BY id DESC LIMIT 20")->fetchAll();
    $last_clicks = $pdo->query("SELECT name, created_at FROM clicks ORDER BY id DESC LIMIT 5")->fetchAll();

    return [
        'total_cents' => (int) ($state['total_cents'] ?? 0),
        'total_clicks' => (int) ($state['total_clicks'] ?? 0),
        'withdrawals' => $withdrawals,
        'last_clicks' => $last_clicks,
    ];
}

function handle_click(PDO $pdo) {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $name = sanitize_name($input['name'] ?? '');
    if ($name === '') {
        http_response_code(422);
        return ['error' => 'Nome é obrigatório'];
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare('INSERT INTO clicks (name, ip_hash) VALUES (?, ?)');
    $ipHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $stmt->execute([$name, $ipHash]);

    $pdo->exec('UPDATE wallet_state SET total_cents = total_cents + 100, total_clicks = total_clicks + 1 WHERE id = 1');
    $pdo->commit();

    return fetch_state($pdo);
}

function handle_withdraw(PDO $pdo) {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $name = sanitize_name($input['name'] ?? '');
    $reason = sanitize_reason($input['reason'] ?? '');
    $amount = (int) ($input['amount'] ?? 0);

    if ($name === '' || $reason === '' || $amount < 1) {
        http_response_code(422);
        return ['error' => 'Nome, motivo e valor são obrigatórios'];
    }

    $pdo->beginTransaction();
    $state = $pdo->query('SELECT total_cents FROM wallet_state WHERE id = 1 FOR UPDATE')->fetch();
    if (!$state) {
        $pdo->rollBack();
        http_response_code(500);
        return ['error' => 'Estado não encontrado'];
    }

    $available = (int) $state['total_cents'];
    $amount_cents = $amount * 100;
    if ($amount_cents > $available) {
        $pdo->rollBack();
        http_response_code(422);
        return ['error' => 'Valor acima do disponível'];
    }

    $stmt = $pdo->prepare('INSERT INTO withdrawals (name, reason, amount_cents) VALUES (?, ?, ?)');
    $stmt->execute([$name, $reason, $amount_cents]);

    $pdo->prepare('UPDATE wallet_state SET total_cents = total_cents - ?, updated_at = NOW() WHERE id = 1')
        ->execute([$amount_cents]);
    $pdo->commit();

    $meme = null;
    if (($available - $amount_cents) === 0) {
        $meme = "$name sacou tudo e deixou todo mundo liso 🤡";
    }

    $resp = fetch_state($pdo);
    if ($meme) {
        $resp['meme'] = $meme;
    }
    return $resp;
}
