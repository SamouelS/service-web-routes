<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

//GET TOUTES LES COLONNES D'UNE TABLE
$app->get('/{[table:patients|personnes|badges|infirmieres|visites}]', function (Request $request, Response $response, array $args) {
    //.preg_replace(RegEx, New, Variable)
    $sqlRequest = 'SELECT * FROM '.preg_replace('/s$/', '', $args['table']);
    $retour = $this->db->query($sqlRequest);
    $json = array();
    foreach ($retour as $row) {
        $json[] = json_encode($row);
    }
    $json = json_encode($json);
    return $json;
    //var_dump($json);
    //var_dump($sqlRequest);
});

//GET LA COLONNE D'UNE TABLE VIA UN ID
$app->get('[/{table:patient|personne|badge|infirmiere|visite}/{id:\d*}]', function (Request $request, Response $response, array $args) {
    
    $sqlRequest = 'SELECT * FROM '.$args['table'].' WHERE id='.$args['id'];
    $retour = $this->db->query($sqlRequest);
    $json= array();
    foreach ($retour as $row) {
        $json[] = json_encode($row);
    }
    $json = json_encode($json);
    return $json;
    //var_dump($sqlRequest);

});

//RENVOIE LES DETAILS D'UNE PERSONNE SI ELLE REUSSI A SE CONNECTER OU STATUS=FALSE SI CELA ECHOUE
$app->get('/connect', function (Request $request, Response $response, array $args) {
    $sqlRequest = ' SELECT * 
                    FROM personne_login pl, personne p
                    where pl.id = p.id';
    $retour = $this->db->query($sqlRequest); 
    $json['status'] = false;
    foreach ($retour as $row) {
        if($row['login'] == $request->getParams()['login'] && $row['mp'] == md5($request->getParams()['mp'])){
            $json['status'] = true;
            $json['personne']=$row;
        }
    }
    //var_dump($json);
    $json = json_encode($json);
    return $json;
});

//UPDATE A FAIRE -> VERIFICATION TYPE
$app->put('[/personnes/{id:\d*}]', function (Request $request, Response $response, array $args) {
    $sqlRequest = 'UPDATE personne SET';
    $retour = $request->getParsedBody();
    $i = count($retour);
    foreach($retour as $paramerte=>$valeur) {
        $i = $i - 1;
        if(gettype($valeur) == "integer") {
            $sqlRequest = $sqlRequest." ".$paramerte." = ".$valeur;
        } else {
            $sqlRequest = $sqlRequest." ".$paramerte." = '".$valeur."'";
        }
        if ($i != 0) {
            $sqlRequest = $sqlRequest.',';
        } else {
            $sqlRequest = $sqlRequest.' WHERE id = '.$args['id'].';';
        }
    }
    if($this->db->query($sqlRequest))
        $vretour = true;
    else
        $vretour = false;
    return $vretour;
});

//DELETE COLONNE VIA ID
$app->delete('[/deletepersonne/{id:\d*}]', function (Request $request, Response $response, array $args) {
    $sqlRequest ='DELETE FROM personne WHERE id = '.$args['id'].';';
    if($this->db->query($sqlRequest))
        $vretour = true;
    else
        $vretour = false;
    return $vretour;
});