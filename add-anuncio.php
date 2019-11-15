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

if(isset($_POST['titulo']) && !empty($_POST['titulo'])){
    $titulo = utf8_decode(addslashes($_POST['titulo']));
    $categoria = addslashes($_POST['categoria']);
    $valor = addslashes($_POST['valor']);
    $descricao = utf8_decode(addslashes($_POST['descricao']));
    $estado = addslashes($_POST['estado']);


    $a->addAnuncio($titulo, $categoria, $valor, $descricao, $estado);
    ?>
    <div class="alert alert-success">
        Produto Adicionado com sucesso!
    </div>
    <?php

}

?>

<div class="container">

    <h1>Meus Anúncios - Adicionar anúncio</h1>
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
                    <option value="<?= $cat['id']; ?>"><?= utf8_encode($cat['nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" class="form-control">
        </div>

        <div class="form-group">
            <label for="valor">Valor:</label>
            <input type="text" name="valor" id="valor" class="form-control">
        </div>

        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label for="estado">Estado de Conservação:</label>
            <select name="estado" id="estado" class="form-control">
                <option value="0">Ruim</option>
                <option value="1">Bom</option>
                <option value="2">Ótimo</option>
            </select>
        </div>
        <input type="submit" value="Adicionar" class="btn btn-default">
    </form>

</div>

<?php require 'pages/footer.php'; ?>