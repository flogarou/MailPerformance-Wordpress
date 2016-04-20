<?php
/*
Plugin Name: MailPerformance
Plugin URI:  http://v8.mailperformance.com/
Description: MailPerformance plugin WordPress
Version:     1.0.0
Author:      NP6
Author URI:  http://www.np6.fr/
License:     MIT License
License URI: http://choosealicense.com/licenses/mit/
*/

defined( 'ABSPATH' ) or die( 'Error 403 : Forbidden' );

class MPerf_Plugin
{
    //Construction de la page
  public function __construct()
  {
    add_action('admin_menu', array($this, 'add_admin_menu'));
  }

    //Ajout de la page dans le menu admin
    public function add_admin_menu()
    {
        add_menu_page('MailPerformance Plugin', 'MailPerformance', 'manage_options', 'zero', array($this, 'menu_html'), 'dashicons-email-alt');
    }

    //Fonction de la page admin
    public function menu_html()
    {
        //Creation des variable dans le cache serveur
        add_option("mperf_name_xkey", null);
        add_option("mperf_name_field", null);

        add_option("mperf_option_email", null);
        add_option("mperf_option_id_action", null);

        //Creation du fichier de sauvegarde + creation des champs de la page admin
        if ((!empty($_POST)) && isset($_POST['MPerfForm']) && ($_POST['MPerfForm'] == "LabelComplete"))
        {
            update_option('mperf_name_xkey', addslashes($_POST['MPerfNameXkey']));
            update_option('mperf_name_field', addslashes($_POST['MPerfNameField']));
            update_option('mperf_option_email', addslashes($_POST['MPerfOptionEmail']));
            update_option('mperf_option_id_action', addslashes($_POST['MPerfOptionIdAction']));

            echo '<p>New values added !</p>';
        }

        //Creation de la page admin en HTML
        echo '<br>
            <img src="' . plugin_dir_url( __FILE__ ) . 'img/mailperf.png" width="140" height="40"/>
            <br>
            <form action="#" method="post">
            <input type="hidden" name="MPerfForm" value="LabelComplete"/>
            <h1>'.get_admin_page_title().'</h1>
            <p>Welcome in MailPerformance plugin for WordPress !</p>
            <p>Here put your Xkey and the Id of the unicity field :</p>
            <p>
                <label for="MPerfXkey">Xkey :</label>
                <input id="MPerfIdXkey" name="MPerfNameXkey" type="string" value="' . get_option("mperf_name_xkey") . '"/>
                <p></p>
                <label for="MPerfIdField">Id field :</label>
                <input id="MPerfIdField" name="MPerfNameField" type="number" value="' . get_option("mperf_name_field") . '"/>
            </p>
            <h1>Alert (option)</h1>
            <p>If the email and the action (mailMessage) are complete, the administrator (email) will receive a mail (Id action) if an action fail.</p>
            <p>Here put your Email and Id action :</p>
            <p>
                <label for="MPerfOptionEmail">Email :</label>
                <input id="MPerfOptionEmail" name="MPerfOptionEmail" type="email" value="' . get_option("mperf_option_email") . '"/>
                <p></p>
                <label for="MPerfOptionIdAction">Id action :</label>
                <input id="MPerfOptionIdAction" name="MPerfOptionIdAction" type="string" value="' . get_option("mperf_option_id_action") . '"/>
            </p>
            <input type="submit"/>
            </form>';
    }

    //Fonction de creation d'une cible
    public function MPerfPostTarget($unicity)
    {
        //On recupere les donnees
        $xKey = get_option("mperf_name_xkey");
        $IdField = get_option("mperf_name_field");

        if ($xKey == null || $IdField == null)
        {
            return (false);
        }


        $urlBase = 'http://v8.mailperformance.com/';

        //Creation du tableau en fonction de l'id des champs de la fiche cible : "id-champ" => "valeur de l'information"
        $data = array($IdField => $unicity);
        $dataJson = json_encode($data);

        //On paramettre la requete 'GET'
        $req = MPerf_Plugin::MPerfGetByUnicity($urlBase, $unicity, $req, $xKey);

        //Execution de la requete
        $result = curl_exec($req);

        //Verification des reponses
        if ($result == false)
        {
            //Affichage de l'erreur
            $info = curl_getinfo($req);

            if ($info['http_code'] == 404)
            {
                //La cible n'existe pas, nous devons la creer

                //On remplit la requete 'POST'
                $req = MPerf_Plugin::MPerfPostOrPutOnTarget($req, 'POST', $dataJson, $xKey, $urlBase);
            }
            else
            {
                //Erreur, on envoie un mail a l'administrateur
                $req = MPerf_Plugin::MPerfOptionMessage($req, $xKey, $urlBase);
            }
        }
        curl_close($req);

        return (true);
    }

    //Utilisation de cURL pour remplir les requetes
    public function MPerfStartCurlInit($url)
    {
        $init = curl_init();
        curl_setopt($init, CURLOPT_URL, $url);
        curl_setopt($init, CURLOPT_RETURNTRANSFER, true);
        return ($init);
    }

    public function MPerfGetByUnicity($urlBase, $unicity, $req, $xKey)
    {
        //On trouve l'adresse pour la requete
        $url = $urlBase . 'targets?unicity='. $unicity;

        //On remplit la requete 'GET'
        $req = MPerf_Plugin::MPerfStartCurlInit($url);
        curl_setopt($req,CURLOPT_CUSTOMREQUEST,'GET');

        //Mise en place de la xKey et des options
        curl_setopt($req, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-Key: ' . $xKey));

        return ($req);
    }

    public function MPerfPostOrPutOnTarget($req, $request, $dataJson, $xKey, $urlBase)
    {
        //Nouvelle url
        $url = $urlBase . 'targets/';

        //On remplit la requete avec le bon verbe ($request) : POST / PUT
        $req = MPerf_Plugin::MPerfStartCurlInit($url);
        curl_setopt($req, CURLOPT_CUSTOMREQUEST, $request);
        curl_setopt($req, CURLOPT_POSTFIELDS, $dataJson);

        //Mise en place du xKey et des options
        curl_setopt($req, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($dataJson),
        'X-Key: ' . $xKey));


        //Execution de la requete
        $result = curl_exec($req);

        //Verification des reponses
        $info = curl_getinfo($req);
        if ($info['http_code'] != 200)
        {
            //Erreur, on envoie un mail a l'administrateur
            $req = MPerf_Plugin::MPerfOptionMessage($req, $xKey, $urlBase);
        }
        return ($req);
    }

    public function MPerfOptionMessage($req, $xKey, $urlBase)
    {
        //On recupere les donnees
        $unicity = get_option("mperf_option_email");
        $idMessage = get_option("mperf_option_id_action");

        if ($unicity == null || $idMessage == null)
        {
            return (false);
        }

        //On parametre la requete 'GET'
        $req = MPerf_Plugin::MPerfGetByUnicity($urlBase, $unicity, $req, $xKey);

        //Execution de la requete
        $result = curl_exec($req);

        //Verification des reponses
        if ($result == false)
        {
            //Affichage de l'erreur
            $info = curl_getinfo($req);
        }
        else
        {
            //On recupere l'id de la cible
            $tab = json_decode($result, TRUE);
            $targetId = $tab['id'];

            //Nouvelle url en fonction de l'id du message et de la cible
            $url = $urlBase . 'actions/' . $idMessage . '/targets/' . $targetId;

            //On remplit la requete
            $req = MPerf_Plugin::MPerfStartCurlInit($url);
            curl_setopt($req, CURLOPT_POST, true);

            //Mise en place du xKey et des options
            curl_setopt($req, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: 0',
            'X-Key: ' . $xKey));

            //Execution de la requete
            $result = curl_exec($req);

            //Verification des reponses
            $info = curl_getinfo($req);
        }
        return ($req);
    }
}

//Lancement du plugin
new MPerf_Plugin();


if ($_GET['email'] != null)
{
    $email = $_GET['email'];
    MPerf_Plugin::MPerfPostTarget($email);
}

?>
