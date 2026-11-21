<?php require 'config/functions.php';
$table = 'enderecos';
$fields = ['nome','tipo','rua','numero','bairro','cidade','estado','cep'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 verifyToken(); $action=$_POST['action']??'save'; $id=(int)($_POST['id']??0);
 try {
  if ($action === 'delete') {
   $s=$pdo->prepare('DELETE FROM enderecos WHERE id=?'); $s->execute([$id]); flash('Registro excluído.');
  } else {
   $values=[]; foreach($fields as $field) $values[] = trim((string)($_POST[$field]??''));
   if (trim((string)($_POST['nome']??''))==='') throw new Exception('Informe o nome.');
   if (empty($_POST['rua']) || empty($_POST['numero']) || empty($_POST['cidade']) || !preg_match('/^[A-Za-z]{2}$/',trim($_POST['estado']??'')) || !in_array($_POST['tipo']??'', ['Origem','Destino','Ambos'],true)) throw new Exception('Confira os campos obrigatórios do endereço.');
   if ($id>0) { $sets=implode(',',array_map(fn($f)=>"$f=?",$fields)); $s=$pdo->prepare("UPDATE enderecos SET $sets WHERE id=?"); $values[]=$id; $s->execute($values); }
   else { $names=implode(',',$fields); $marks=implode(',',array_fill(0,count($fields),'?')); $s=$pdo->prepare("INSERT INTO enderecos ($names) VALUES ($marks)"); $s->execute($values); }
   flash('Registro salvo com sucesso.');
  }
 } catch (PDOException $e) { flash('Não foi possível excluir ou salvar. O registro pode estar sendo usado em um frete.','danger'); }
 catch (Exception $e) { flash($e->getMessage(),'danger'); }
 redirect('enderecos.php');
}
$edit = isset($_GET['editar']) ? one('enderecos',(int)$_GET['editar']) : null;
$items=all('enderecos'); require 'header.php'; ?>
<section class="hero d-flex justify-content-between align-items-center flex-wrap gap-2"><div><div class="eyebrow">CADASTROS</div><h1>Endereços</h1><p class="text-secondary">Cadastre, consulte e edite os registros.</p></div><a class="btn btn-outline-primary" href="index.php">← Início</a></section>
<div class="row g-4"><div class="col-lg-5"><div class="panel"><h2 class="h5 fw-bold mb-3"><?= $edit?'Editar registro':'Novo cadastro' ?></h2><form method="post"><input type="hidden" name="csrf" value="<?= h(token()) ?>"><input type="hidden" name="id" value="<?= h($edit['id']??0) ?>">
<div class="mb-3"><label class="form-label" for="nome">Identificação do local</label><input class="form-control" id="nome" name="nome" type="text" value="<?= h($edit['nome']??'') ?>" required></div>
<div class="mb-3"><label class="form-label" for="tipo">Tipo de endereço</label><select class="form-select" id="tipo" name="tipo"><?php foreach (['Origem','Destino','Ambos'] as $opt): ?><option value="<?= $opt ?>" <?= ($edit['tipo']??'Ambos')===$opt?'selected':'' ?>><?= $opt ?></option><?php endforeach; ?></select></div>
<div class="mb-3"><label class="form-label" for="rua">Rua / avenida</label><input class="form-control" id="rua" name="rua" type="text" value="<?= h($edit['rua']??'') ?>" required></div>
<div class="mb-3"><label class="form-label" for="numero">Número</label><input class="form-control" id="numero" name="numero" type="text" value="<?= h($edit['numero']??'') ?>" required></div>
<div class="mb-3"><label class="form-label" for="bairro">Bairro</label><input class="form-control" id="bairro" name="bairro" type="text" value="<?= h($edit['bairro']??'') ?>" ></div>
<div class="mb-3"><label class="form-label" for="cidade">Cidade</label><input class="form-control" id="cidade" name="cidade" type="text" value="<?= h($edit['cidade']??'') ?>" required></div>
<div class="mb-3"><label class="form-label" for="estado">Estado (UF)</label><input class="form-control" id="estado" name="estado" type="text" value="<?= h($edit['estado']??'') ?>" required maxlength="2"></div>
<div class="mb-3"><label class="form-label" for="cep">CEP</label><input class="form-control" id="cep" name="cep" type="text" value="<?= h($edit['cep']??'') ?>" ></div>

<button class="btn btn-primary w-100" type="submit">Salvar</button><?php if($edit): ?><a class="btn btn-link w-100" href="enderecos.php">Cancelar edição</a><?php endif; ?></form></div></div><div class="col-lg-7"><div class="panel"><h2 class="h5 fw-bold mb-3">Registros cadastrados (<?= count($items) ?>)</h2><?php if(!$items): ?><p class="text-secondary">Nenhum registro ainda.</p><?php else: ?><div class="table-responsive"><table class="table table-hover"><thead><tr><th>ID</th><th>Nome</th><th>Detalhes</th><th>Ações</th></tr></thead><tbody><?php foreach($items as $r): ?><tr><td><?= $r['id'] ?></td><td><?= h($r['nome']) ?></td><td><?= h($r['tipo'].' • '.$r['cidade'].'/'.$r['estado']) ?></td><td><div class="d-flex gap-1"><a class="btn btn-sm btn-outline-primary" href="enderecos.php?editar=<?= $r['id'] ?>">Editar</a><form method="post" onsubmit="return confirm('Excluir este registro?')"><input type="hidden" name="csrf" value="<?= h(token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-outline-danger">Excluir</button></form></div></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div></div></div>
<?php require 'footer.php'; ?>