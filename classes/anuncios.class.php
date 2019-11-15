<?php

class Anuncios{

    public function getTotalAnuncios($filtros){

        global $pdo;

        $filtrostring = array('1=1');
        if(!empty($filtros['categoria'])){
            $filtrostring[] = 'anuncios.id_categoria = :id_categoria';
        }

        if(!empty($filtros['valor'])){
            $filtrostring[] = 'anuncios.valor BETWEEN :preco1 AND :preco2';
        }

        if(!empty($filtros['estado'])){
            $filtrostring[] = 'anuncios.estado = :estado';
        }

        $sql = $pdo->prepare("SELECT COUNT(*) as c FROM anuncios WHERE ".implode(' AND ', $filtrostring)."");

        if(!empty($filtros['categoria'])){
            $sql->bindValue(':id_categoria', $filtros['categoria']);
        }

        if(!empty($filtros['valor'])){
            $preco = explode('-', $filtros['preco']);
            $sql->bindValue(':preco1', $preco[0]);
            $sql->bindValue(':preco2', $preco[1]);
        }

        if(!empty($filtros['estado'])){
            $sql->bindValue(':estado', $filtros['estado']);
        }

        $sql->execute();
        $row = $sql->fetch();

        return $row['c'];

    }

    public function getUltimosAnuncios($page, $perPage, $filtros){
        global $pdo;

        $offset = ($page - 1) * $perPage;

        $filtrostring = array('1=1');
        if(!empty($filtros['categoria'])){
            
            $filtrostring[] = 'anuncios.id_categoria = :id_categoria';
        }

        if(!empty($filtros['preco'])){
            $filtrostring[] = 'anuncios.valor BETWEEN :preco1 AND :preco2';
        }

        if(!empty($filtros['estado'])){
            $filtrostring[] = 'anuncios.estado = :estado';
        }

        $sql = $pdo->prepare("SELECT *, (select anuncios_imagens.url from anuncios_imagens where anuncios_imagens.id_anuncio = anuncios.id limit 1) as url, (select categorias.nome from categorias where categorias.id = anuncios.id_categoria) as categoria FROM anuncios WHERE ".implode(' AND ', $filtrostring)." ORDER BY anuncios.id DESC LIMIT $offset, $perPage");
        
        if(!empty($filtros['categoria'])){
            $sql->bindValue(':id_categoria', $filtros['categoria']);
        }

        if(!empty($filtros['preco'])){
            $preco = explode('-', $filtros['preco']);
            $sql->bindValue(':preco1', $preco[0]);
            $sql->bindValue(':preco2', $preco[1]);
        }

        if(!empty($filtros['estado'])){
            $sql->bindValue(':estado', $filtros['estado']);
        }

        $sql->execute();

        $array = array();
        if($sql->rowCount() > 0){
            $array = $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        return $array;
    }

    public function getMeusAnuncios(){
        global $pdo;

        $sql = $pdo->prepare('SELECT *, (select anuncios_imagens.url from anuncios_imagens where anuncios_imagens.id_anuncio = anuncios.id limit 1) as url FROM anuncios WHERE id_usuario = :id_usuario');
        $sql->bindValue(':id_usuario', $_SESSION['cLogin']);
        $sql->execute();

        $array = array();
        if($sql->rowCount() > 0){
            $array = $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        return $array;

    }

    public function getAnuncio($id){

        global $pdo;
        $array = array();
        $sql = $pdo->prepare('SELECT *, (select categorias.nome from categorias where categorias.id = anuncios.id_categoria) as categoria, (select usuarios.telefone from usuarios where usuarios.id = anuncios.id_usuario) as telefone FROM anuncios WHERE id = :id');
        $sql->bindValue(':id', $id);
        $sql->execute();

        $array = array();
        if($sql->rowCount() > 0){
            $array = $sql->fetch(PDO::FETCH_ASSOC);
            $array['fotos'] = array();

            $sql = $pdo->prepare('SELECT id, url FROM anuncios_imagens WHERE id_anuncio = :id_anuncio');
            $sql->bindValue(':id_anuncio', $id);
            $sql->execute();

            if($sql->rowCount() > 0){
                $array['fotos'] = $sql->fetchAll(PDO::FETCH_ASSOC);
            }

        }

        return $array;

    }

    public function addAnuncio($titulo, $categoria, $valor, $descricao, $estado){
        global $pdo;
        $sql = $pdo->prepare('INSERT INTO anuncios SET titulo = :titulo, id_categoria = :id_categoria, valor = :valor, id_usuario = :id_usuario, descricao = :descricao, estado = :estado');
        $sql->bindValue(':titulo', $titulo);
        $sql->bindValue(':id_categoria', $categoria);
        $sql->bindValue(':valor', $valor);
        $sql->bindValue(':id_usuario', $_SESSION['cLogin']);
        $sql->bindValue(':descricao', $descricao);
        $sql->bindValue(':estado', $estado);
        $sql->execute();
    }

    public function editAnuncio($titulo, $categoria, $valor, $descricao, $estado, $fotos, $id){
        global $pdo;
        $sql = $pdo->prepare('UPDATE anuncios SET titulo = :titulo, id_categoria = :id_categoria, valor = :valor, id_usuario = :id_usuario, descricao = :descricao, estado = :estado WHERE id = :id');
        $sql->bindValue(':titulo', $titulo);
        $sql->bindValue(':id_categoria', $categoria);
        $sql->bindValue(':valor', $valor);
        $sql->bindValue(':id_usuario', $_SESSION['cLogin']);
        $sql->bindValue(':descricao', $descricao);
        $sql->bindValue(':estado', $estado);
        $sql->bindValue(':id', $id);
        $sql->execute();

        if(count($fotos) > 0){

            for($i=0; $i<count($fotos['tmp_name']); $i++){
                $tipo = $fotos['type'][$i];
                if(in_array($tipo, array('image/jpeg', 'image/png'))){

                    $tmpname = md5(time() . rand(0, 999)).'.jpg';
                    move_uploaded_file($fotos['tmp_name'][$i], 'assets/images/anuncios/'.$tmpname);

                    list($width_orig, $height_orig) = getimagesize('assets/images/anuncios/'.$tmpname);
                    $ratio = $width_orig / $height_orig;
                    
                    $width = 500;
                    $height = 500;

                    if(($width / $height) > $ratio){
                        $width = $height*$ratio;
                    }else{
                        $height = $width/$ratio;
                    }

                    $img = imagecreatetruecolor($width, $height);

                    if($tipo == 'image/jpeg'){
                        $origi = imagecreatefromjpeg('assets/images/anuncios/'.$tmpname);
                    }else if($tipo == 'image/png'){
                        $origi = imagecreatefrompng('assets/images/anuncios/'.$tmpname);
                    }

                    imagecopyresampled($img, $origi, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

                    imagejpeg($img, 'assets/images/anuncios/'.$tmpname, 80);

                    $sql = $pdo->prepare("INSERT INTO anuncios_imagens SET id_anuncio = :id_anuncio, url = :url");
                    $sql->bindValue(':id_anuncio', $id);
                    $sql->bindValue(':url', $tmpname);
                    $sql->execute();

                }
            }

        }

    }

    public function excluirAnuncio($id){
        global $pdo;
        
        $sql = $pdo->prepare("DELETE FROM anuncios_imagens WHERE id_anuncio = :id_anuncio");
        $sql->bindValue(":id_anuncio", $id);
        $sql->execute();

        $sql = $pdo->prepare("DELETE FROM anuncios WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();

    }

    public function excluirFoto($id){
        global $pdo;
        
        $id_anuncio = 0;

        $sql = $pdo->prepare("SELECT id_anuncio FROM anuncios_imagens WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();

        if($sql->rowCount() > 0){
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $id_anuncio = $row['id_anuncio'];
        }

        $sql = $pdo->prepare("DELETE FROM anuncios_imagens WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();

        return $id_anuncio;

    }

}
?>