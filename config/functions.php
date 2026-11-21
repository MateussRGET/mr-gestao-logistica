<?php
session_start();
require_once __DIR__ . '/db.php';
function h($s) { return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); }
function redirect($path) { header('Location: ' . $path); exit; }
function flash($message, $type = 'success') { $_SESSION['flash'] = [$message, $type]; }
function token() { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24)); return $_SESSION['csrf']; }
function verifyToken() { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(403); exit('Formulário inválido. Atualize a página.'); } }
function all($table) { global $pdo; if (!in_array($table, ['produtos','enderecos','transportadores'], true)) throw new Exception('Tabela inválida'); return $pdo->query("SELECT * FROM $table ORDER BY id DESC")->fetchAll(); }
function one($table,$id) { global $pdo; if (!in_array($table, ['produtos','enderecos','transportadores'], true)) throw new Exception('Tabela inválida'); $s=$pdo->prepare("SELECT * FROM $table WHERE id=?"); $s->execute([$id]); return $s->fetch(); }
function money($n) { return 'R$ ' . number_format((float)$n,2,',','.'); }
