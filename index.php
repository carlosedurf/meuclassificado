<?php require 'pages/header.php'; 
require 'classes/anuncios.class.php';
require 'classes/usuarios.class.php';
require 'classes/categorias.class.php';
$a = new Anuncios();
$u = new Usuarios();
$c = new Categorias();

$filtros = array(
    'categoria' => '',
    'preco' => '',
    'estado' => ''
);
if(isset($_GET['filtros'])){
    $filtros = $_GET['filtros'];
}

$total_anuncios = $a->getTotalAnuncios($filtros);
$total_usuario = $u->getTotalUsuarios();

$p = 1;
if(isset($_GET['p']) && !empty($_GET['p'])){
    $p = addslashes($_GET['p']);
}
$porPagina = 2;
$total_paginas = ceil($total_anuncios / $porPagina);

$anuncios = $a->getUltimosAnuncios($p, $porPagina, $filtros);
$categorias = $c->getLista();

?>

    <div class="container-fluid">
        <div class="jumbotron">
            <h2>Nós temos hoje <?= $total_anuncios; ?> Anúncios!</h2>
            <p>E mais de <?= $total_usuario; ?> usuários cadastrados.</p>
        </div>

        <div class="row">
            <div class="col-sm-3">
                <h4>Pesquisa Avançada</h4>
                <form action="" method="GET">
                    <div class="form-group">
                        <label for="categoria">Categoria:</label>
                        <select name="filtros[categoria]" id="categoria" class="form-control">
                        <option value=""></option>
                            <?php foreach($categorias as $cat): ?>
                                <option <?= ($filtros['categoria'] == $cat['id']) ? 'selected="selected"' : ''; ?> value="<?= $cat['id']; ?>"><?= utf8_encode($cat['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="preco">Preço:</label>
                        <select name="filtros[preco]" id="preco" class="form-control">
                        <option value=""></option>
                        <option <?= ($filtros['preco'] == '0-50') ? 'selected="selected"': ''; ?> value="0-50">R$ 0 - 50</option>
                        <option <?= ($filtros['preco'] == '51-100') ? 'selected="selected"': ''; ?> value="51-100">R$ 51 - 100</option>
                        <option <?= ($filtros['preco'] == '101-200') ? 'selected="selected"': ''; ?> value="101-200">R$ 101 - 200</option>
                        <option <?= ($filtros['preco'] == '201-500') ? 'selected="selected"': ''; ?> value="201-500">R$ 201 - 500</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado de Conservação:</label>
                        <select name="filtros[estado]" id="estado" class="form-control">
                        <option value=""></option>
                        <option <?= ($filtros['estado'] == '0') ? 'selected="selected"': ''; ?> value="0">Ruim</option>
                        <option <?= ($filtros['estado'] == '1') ? 'selected="selected"': ''; ?> value="1">Bom</option>
                        <option <?= ($filtros['estado'] == '2') ? 'selected="selected"': ''; ?> value="2">Ótimo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="submit" value="Buscar" class="btn btn-info">
                    </div>
                </form>

                
            </div>
            <div class="col-sm-9">
                <h4>Últimos Anúncios</h4>

                <table class="table table-striped">
                    <tbody>
                        <?php foreach($anuncios as $anuncio): ?>
                            <tr>
                                <td>
                                    <?php if(!empty($anuncio['url'])): ?>
                                        <img src="assets/images/anuncios/<?= $anuncio['url']; ?>" height="50" border="0">
                                    <?php else: ?>
                                        <img src="assets/images/default.png" border="0" height="50">
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="produto.php?id=<?= utf8_encode($anuncio['id']); ?>"><?= utf8_encode($anuncio['titulo']); ?></a><br>
                                    <?= utf8_encode($anuncio['categoria']); ?>
                                </td>

                                <td style="line-height: 50px;">
                                    R$ <?= number_format($anuncio['valor'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <ul class="pagination">
                    <?php for($i=1; $i<=$total_paginas; $i++): ?>
                        <li class="<?= ($p == $i) ? 'active' : ''; ?>"><a href="index.php?<?php 
                            $w = $_GET;
                            $w['p'] = $i;
                            echo http_build_query($w);
                        ?>"><?= $i; ?></a></li>
                    <?php endfor; ?>
                </ul>

            </div>
        </div>
    </div>
    
<?php require 'pages/footer.php'; ?>