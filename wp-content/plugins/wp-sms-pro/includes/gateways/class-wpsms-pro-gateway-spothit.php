<?php

namespace WP_SMS\Gateway;

class spothit extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://www.spot-hit.fr/api";
    public $tariff = "https://www.spot-hit.fr/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = 'ex : +33600000000,003360-00-00-00 , 6 00 00 00 00';
        $this->has_key        = true;
        $this->gatewayFields  = [
            'has_key' => [
                'id'   => 'gateway_key',
                'name' => 'Key',
                'desc' => 'Votre clé API d\'identification',
            ],
            'from'    => [
                'id'   => 'gateway_sender_id',
                'name' => 'Sender number',
                'desc' => 'Sender number or sender ID',
            ],
        ];
    }

    public function SendSMS()
    {

        /**
         * Modify sender number
         *
         * @param string $this ->from sender number.
         * @since 3.4
         *
         */
        $this->from = apply_filters('wp_sms_from', $this->from);

        /**
         * Modify Receiver number
         *
         * @param array $this ->to receiver number
         * @since 3.4
         *
         */
        $this->to = apply_filters('wp_sms_to', $this->to);

        /**
         * Modify text message
         *
         * @param string $this ->msg text message.
         * @since 3.4
         *
         */
        $this->msg = apply_filters('wp_sms_msg', $this->msg);

        // Get the credit.
        $credit = $this->GetCredit();

        // Check gateway credit
        if (is_wp_error($credit)) {
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');
            return $credit;
        }

        $response = wp_remote_get(add_query_arg([
            'key'           => $this->has_key,
            'destinataires' => implode(',', $this->to),
            'message'       => urlencode($this->msg),
            'expediteur'    => $this->from,
            'date'          => '',
        ], "{$this->wsdl_link}/envoyer/sms"));

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $responseArray = json_decode($response['body'], true);

        if ($responseArray['resultat'] === 0 && isset($responseArray['erreurs'])) {
            return new \WP_Error('send-sms', $this->getErrorMessage($responseArray['erreurs']));
        }

        $this->log($this->from, $this->msg, $this->to, $responseArray);

        /**
         * Run hook after send sms.
         *
         * @param string $result result output.
         * @since 2.4
         *
         */
        do_action('wp_sms_send', $responseArray);

        return $responseArray;
    }

    private function getErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case 1:
                return 'Type de message non spécifié ou incorrect (paramètre "type")';
            case 2:
                return 'Le message est vide';
            case 3:
                return 'Le message contient plus de 160 caractères (70 en unicode)';
            case 4:
                return 'Aucun destinataire valide n\'est renseigné';
            case 5:
                return 'Numéro interdit';
            case 6:
                return 'Numéro de destinataire invalide';
            case 7:
                return 'Votre compte n\'a pas de formule définie';
            case 8:
                return 'SMS L\'expéditeur est invalide.';
            case 9:
                return 'Le système a rencontré une erreur, merci de nous contacter.';
            case 10:
                return 'Vous ne disposez pas d\'assez de crédits pour effectuer cet envoi.';
            case 11:
                return 'L\'envoi des message est désactivé pour la démonstration.';
            case 12:
                return 'Votre compte a été suspendu. Contactez-nous pour plus d\'informations';
            case 13:
            case 14:
            case 15:
                return 'Votre limite d\'envoi paramétrée est atteinte. Contactez-nous pour plus d\'informations.';
            case 16:
                return 'Le paramètre "smslongnbr" n\'est pas cohérent avec la taille du message envoyé.';
            case 17:
                return 'L\'expéditeur n\'est pas autorisé.';
            case 18:
                return 'EMAIL | Le sujet est trop court.';
            case 19:
                return 'EMAIL | L\'email de réponse est invalide.';
            case 20:
                return 'EMAIL | Le nom d\'expéditeur est trop court.';
            case 21:
                return 'Token invalide. Contactez-nous pour plus d\'informations.';
            case 22:
                return 'Durée du message non autorisée. Contactez-nous pour plus d\'informations.';
            case 23:
                return 'Aucune date variable valide n\'a été trouvée dans votre liste de destinataires.';
            case 24:
                return 'Votre campagne n\'a pas été validée car il manque la mention « STOP au 36200 » dans votre message. Pour rappel, afin de respecter les obligations légales de la CNIL, il est impératif d\'inclure une mention de désinscription.';
            case 25:
                return 'Echelonnage : date de début vide';
            case 26:
                return 'Echelonnage : date de fin vide';
            case 27:
                return 'Echelonnage : date de début plus tard que date de fin';
            case 28:
                return 'Echelonnage : aucun créneau disponible';
            case 29:
                return 'MMS : Le mot "virtual" peut générer des anomalies dans le routage de vos messages. Nous vous invitons à utiliser un synonyme ou une autre écriture (Virtuel par exemple). Nous sommes en train de corriger cette anomalie, veuillez-nous excuser pour la gêne occasionnée.';
            case 30:
                return 'Clé API non reconnue.';
            case 36:
                return 'Vous ne pouvez pas avoir d\'emojis dans votre message.';
            case 38:
                return 'Vous devez ajouter une mention "Stop" à votre SMS.';
            case 40:
                return 'Une pièce jointe ne vous appartient pas.';
            case 41:
                return 'Une pièce jointe est invalide.';
            case 45:
                return 'Ce produit n\'est pas activé.';
            case 50:
                return 'Le fuseau horaire spécifié n\'est pas valide.';
            case 51:
                return 'La date est déjà passée après calcule du fuseau horaire.';
            case 52:
                return 'Vous avez atteint la limite maximale de 50 campagnes en brouillons. Si vous souhaitez en ajouter plus, merci de nous contacter.';
            case 53:
                return 'Nous limitons à 5 pièces jointes par campagne email.';
            case 61:
                return 'Nous avons détecté un lien dans le contenu de votre message, merci de vous rapprocher de notre service client pour valider cet envoi.';
            case 62:
                return 'Votre limite d\'envoi est atteinte.';
            case 63:
                return 'Vous avez dépassé votre limite de requêtes api.';
            case 65:
                return 'Une maintenance est prévu sur ce créneaux horaire.';
            case 66:
                return 'Nous avons bloqué préventivement cette campagne car elle présente des caractéristiques similaires à une campagne déjà envoyée (contenu, destinataires...). Merci de nous contacter pour plus d\'informations.';
            case 99:
                return 'Votre compte est suspendu.';
            case 100:
                return 'Ip non autorisée.';
            default:
                return $errorCode;
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms'));
        }

        $response = wp_remote_get($this->wsdl_link . "/credits?key={$this->has_key}");

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $responseArray = json_decode($response['body'], true);

        if ($responseArray['resultat'] === 0 && isset($responseArray['erreurs'])) {
            return new \WP_Error('account-credit', $this->getErrorMessage($responseArray['erreurs']));
        }

        return $responseArray['euros'];
    }
}