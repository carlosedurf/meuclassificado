<?php require 'pages/header.php'; ?>
<?php

if(empty($_SESSION['cLogin'])){
    ?>
    <script>
        window.location.href = 'login.php';
    </script>
    <?php
    exit;
}

require 'classes/anuncios.class.php';
$a = new Anuncios();

if(isset($_GET['id']) && !empty($_GET['id'])){
    $id = addslashes($_GET['id']);

    $info = $a->getAnuncio($id);
    
}else{
    ?>
    <script>
        window.location.href = 'meus-anuncios.php';
    </script>
    <?php
    exit;
}

if(isset($_POST['titulo']) && !empty($_POST['titulo'])){
    $titulo = addslashes($_POST['titulo']);
    $categoria = addslashes($_POST['categoria']);
    $valor = addslashes($_POST['valor']);
    $descricao = addslashes($_POST['descricao']);
    $estado = addslashes($_POST['estado']);

    if(isset($_FILES['fotos'])){
        $fotos = $_FILES['fotos'];
    }else{
        $fotos = array();
    }

    $a->editAnuncio($titulo, $categoria, $valor, $descricao, $estado, $fotos, $id);
    ?>
    <div class="alert alert-success">
        Produto Editado com sucesso!
    </div>
    <?php

}

?>

<div class="container">

    <h1>Meus Anúncios - Editar Anúncio</h1>
    <hr>

    <form method="post" enctype="multipart/form-data">
    
        <div class="form-group">
            <label for="categoria">Categoria:</label>
            <select name="categoria" id="categoria" class="form-control">
                <?php
                require 'classes/categorias.class.php';
                $c = new Categorias();
                $cats = $c->getLista();
                ?>
                <?php foreach($cats as $cat): ?>
                    <option <?= ($cat['id'] == $info['id_categoria']) ? 'selected="selected"' : ''; ?> value="<?= $cat['id']; ?>"><?= utf8_encode($cat['nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" value="<?= utf8_encode($info['titulo']); ?>" id="titulo" class="form-control">
        </div>

        <div class="form-group">
            <label for="valor">Valor:</label>
            <input type="text" name="valor" value="<?= $info['valor']; ?>" id="valor" class="form-control">
        </div>

        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" class="form-control"><?= utf8_encode($info['descricao']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="estado">Estado de Conservação:</label>
            <select name="estado" id="estado" class="form-control">
                <option <?= (0 == $info['estado']) ? 'selected="selected"' : ''; ?>  value="0">Ruim</option>
                <option <?= (1 == $info['estado']) ? 'selected="selected"' : ''; ?>  value="1">Bom</option>
                <option <?= (2 == $info['estado']) ? 'selected="selected"' : ''; ?>  value="2">Ótimo</option>
            </select>
        </div>

        <div class="form-group">
            <label for="add_fotos">Fotos do anúncio:</label>
            <input type="file" name="fotos[]" multiple id="add_fotos">
            <br>
            <div class="panel panel-default">
                <div class="panel-heading">Fotos do Anúncio</div>
                <div class="panel-body">
                    <?php foreach($info['fotos'] as $foto): ?>
                        <div class="foto_item">
                            <img src="assets/images/anuncios/<?= $foto['url']; ?>" border="0" class="img-thumbnail"><br>
                            <a href="excluir-foto.php?id=<?= $foto['id']; ?>" class="btn btn-default">Excluir Imagem</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <input type="submit" value="Salvar" class="btn btn-default">
    </form>

</div>

<?php require 'pages/footer.php'; ?>