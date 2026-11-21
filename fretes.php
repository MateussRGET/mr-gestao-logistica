<?php require 'config/functions.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verifyToken();
 try {
  $action=$_POST['action']??'create';
  if ($action==='status') {
   $status=$_POST['status']??''; if(!in_array($status,['Pendente','Em trânsito','Entregue'],true)) throw new Exception('Status inválido.');
   $s=$pdo->prepare('UPDATE fretes SET status=? WHERE id=?'); $s->execute([$status,(int)$_POST['id']]); flash('Status atualizado.');
  } elseif ($action==='delete') {
   $s=$pdo->prepare('DELETE FROM fretes WHERE id=?'); $s->execute([(int)$_POST['id']]); flash('Frete excluído.');
  } else {
   $transportador=(int)($_POST['transportador_id']??0); $origem=(int)($_POST['origem_id']??0); $destino=(int)($_POST['destino_id']??0);
   $data=$_POST['data_frete']??''; $date=DateTime::createFromFormat('!Y-m-d',$data);
   if (!$transportador||!$origem||!$destino||$origem===$destino||!$date||$date->format('Y-m-d')!==$data) throw new Exception('Preencha transportador, endereços diferentes e uma data válida.');
   $ids=$_POST['produto_id']??[]; $qtds=$_POST['quantidade']??[]; $selected=[];
   foreach($ids as $i=>$id) { $id=(int)$id; $q=(int)($qtds[$i]??0); if ($id<=0||$q<=0) throw new Exception('Selecione produtos e quantidades válidas.'); $selected[$id]=($selected[$id]??0)+$q; }
   if (!$selected) throw new Exception('Adicione pelo menos um produto.');
   $pdo->beginTransaction();
   $s=$pdo->prepare('INSERT INTO fretes (transportador_id,origem_id,destino_id,data_frete,observacoes) VALUES (?,?,?,?,?)');
   $s->execute([$transportador,$origem,$destino,$data,trim($_POST['observacoes']??'')]); $freteId=$pdo->lastInsertId();
   $s=$pdo->prepare('INSERT INTO frete_produtos (frete_id,produto_id,quantidade) VALUES (?,?,?)');
   foreach($selected as $id=>$q) $s->execute([$freteId,$id,$q]);
   $pdo->commit(); flash('Frete criado com sucesso.');
  }
 } catch(Throwable $e) { if($pdo->inTransaction()) $pdo->rollBack(); flash($e instanceof PDOException?'Confira os cadastros relacionados.':$e->getMessage(),'danger'); }
 redirect('fretes.php');
}
$produtos=all('produtos');$enderecos=all('enderecos');$transportadores=all('transportadores');
$fretes=$pdo->query("SELECT f.*,t.nome AS transportador,o.nome AS origem,d.nome AS destino, (SELECT COALESCE(SUM(fp.quantidade),0) FROM frete_produtos fp WHERE fp.frete_id=f.id) AS itens, (SELECT GROUP_CONCAT(CONCAT(p.nome,' (',fp.quantidade,'x)') SEPARATOR ', ') FROM frete_produtos fp JOIN produtos p ON p.id=fp.produto_id WHERE fp.frete_id=f.id) AS produtos FROM fretes f JOIN transportadores t ON t.id=f.transportador_id JOIN enderecos o ON o.id=f.origem_id JOIN enderecos d ON d.id=f.destino_id ORDER BY f.id DESC")->fetchAll();
require 'header.php'; ?>
<section class="hero"><div class="eyebrow">OPERAÇÃO</div><h1>Fretes</h1><p class="text-secondary">Vincule um transportador, uma origem, um destino e vários produtos.</p></section>
<div class="panel mb-4"><h2 class="h5 fw-bold mb-3">Gerar novo frete</h2><?php if(!$produtos||count($enderecos)<2||!$transportadores): ?><div class="alert alert-warning">Antes de gerar um frete, cadastre ao menos um produto, dois endereços e um transportador.</div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= h(token()) ?>"><div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label">Transportador</label><select class="form-select" name="transportador_id" required><option value="">Selecione...</option><?php foreach($transportadores as $t): ?><option value="<?= $t['id'] ?>"><?= h($t['nome']) ?></option><?php endforeach; ?></select></div><div class="col-md-6"><label class="form-label">Data do frete</label><input class="form-control" type="date" name="data_frete" value="<?= date('Y-m-d') ?>" required></div><div class="col-md-6"><label class="form-label">Origem</label><select class="form-select" name="origem_id" required><option value="">Selecione...</option><?php foreach($enderecos as $e): if(in_array($e['tipo'],['Origem','Ambos'])): ?><option value="<?= $e['id'] ?>"><?= h($e['nome'].' — '.$e['cidade']) ?></option><?php endif; endforeach; ?></select></div><div class="col-md-6"><label class="form-label">Destino</label><select class="form-select" name="destino_id" required><option value="">Selecione...</option><?php foreach($enderecos as $e): if(in_array($e['tipo'],['Destino','Ambos'])): ?><option value="<?= $e['id'] ?>"><?= h($e['nome'].' — '.$e['cidade']) ?></option><?php endif; endforeach; ?></select></div></div>
<h3 class="h6 fw-bold">Produtos do frete</h3><div id="product-rows"></div><button class="btn btn-outline-primary btn-sm mb-3" type="button" id="add-product">+ Adicionar produto</button><template id="product-row-template"><div class="product-row row g-2 align-items-end"><div class="col-sm-7"><label class="form-label">Produto</label><select class="form-select" name="produto_id[]" required><option value="">Selecione...</option><?php foreach($produtos as $p): ?><option value="<?= $p['id'] ?>"><?= h($p['nome']) ?></option><?php endforeach; ?></select></div><div class="col-sm-3"><label class="form-label">Quantidade</label><input class="form-control" type="number" name="quantidade[]" value="1" min="1" required></div><div class="col-sm-2"><button type="button" class="btn btn-outline-danger w-100 remove-product">Remover</button></div></div></template>
<div class="mb-3"><label class="form-label">Observações</label><textarea class="form-control" name="observacoes" rows="2"></textarea></div><button class="btn btn-primary" type="submit" <?= (!$produtos||count($enderecos)<2||!$transportadores)?'disabled':'' ?>>Gerar frete</button></form></div>
<div class="panel"><h2 class="h5 fw-bold mb-3">Fretes cadastrados</h2><?php if(!$fretes): ?><p class="text-secondary">Nenhum frete cadastrado.</p><?php else: ?><div class="table-responsive"><table class="table"><thead><tr><th>Nº / Data</th><th>Rota</th><th>Transportador</th><th>Produtos</th><th>Status</th><th>Ação</th></tr></thead><tbody><?php foreach($fretes as $f): ?><tr><td>#<?= $f['id'] ?><br><small><?= date('d/m/Y',strtotime($f['data_frete'])) ?></small></td><td><?= h($f['origem']) ?> → <?= h($f['destino']) ?></td><td><?= h($f['transportador']) ?></td><td><small><?= h($f['produtos']) ?></small></td><td><form method="post"><input type="hidden" name="csrf" value="<?= h(token()) ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= $f['id'] ?>"><select class="form-select form-select-sm" name="status" onchange="this.form.submit() "><?php foreach(['Pendente','Em trânsito','Entregue'] as $status): ?><option <?= $f['status']===$status?'selected':'' ?>><?= $status ?></option><?php endforeach; ?></select></form></td><td><form method="post" onsubmit="return confirm('Excluir frete?')"><input type="hidden" name="csrf" value="<?= h(token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $f['id'] ?>"><button class="btn btn-sm btn-outline-danger">Excluir</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
<?php require 'footer.php'; ?>