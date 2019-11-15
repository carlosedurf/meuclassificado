<?php require 'pages/header.php'; 
require 'classes/anuncios.class.php';
require 'classes/usuarios.class.php';
$a = new Anuncios();
$u = new Usuarios();

if(isset($_GET['id']) && !empty($_GET['id'])){
    $id = addslashes($_GET['id']);
}else{
    ?>
    <script>
        window.location.href = 'index.php';
    </script>
    <?php
    exit;
}

$info  = $a->getAnuncio($id);

?>

    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-5">
                <div class="carousel slide" data-ride="carousel" id="meuCarousel">
                    <div class="carousel-inner" role="listbox">
                        <?php foreach($info['fotos'] as $chave => $foto): ?>
                            <div class="item <?= ($chave == '0') ? 'active' : ''; ?>">
                                <img src="assets/images/anuncios/<?= $foto['url']; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="#meuCarousel" class="left carousel-control" role="button" data-slide="prev"><span><</span></a>
                    <a href="#meuCarousel" class="right carousel-control" role="button" data-slide="next"><span>></span></a>
                </div>
            </div>
            <div class="col-sm-7">
                <h1><?= utf8_encode($info['titulo']); ?></h1>
                <h4><?= utf8_encode($info['categoria']); ?></h4>
                <p><?= utf8_encode($info['descricao']); ?></p>
                <br>
                <h3>R$ <?= number_format($info['valor'], 2, ',', '.'); ?></h3>
                <h4>Telefone: <?= $info['telefone']; ?></h4>
            </div>
        </div>
    </div>
    
<?php require 'pages/footer.php'; ?>