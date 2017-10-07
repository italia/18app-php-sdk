Documentazione Per 18appSDK
===========================

Introduzione
------------

Per utilizzare questa libreria bisogna includere nel progetto il file
*18appSDK.php* attraverso :

    ini_set('soap.wsdl_cache_enabled',0); //
    ini_set('soap.wsdl_cache_ttl',0);
    include('18appSDK.php');

Successivamente si deve creare un oggetto di tipo *app18* attraverso :

    $location="Url sito di controllo voucher";
    $certificato="Posizione certificato";
    $pswd="Password del certificato";
    $pi="Partita iva esercente";
    $log_path="Posizione file log";


    $test= new app18($location, $certificato, $pswd, $pi, $log_path);

Hai appena creato un oggetto. I suoi metodi sono :

    Operazione_Di_Controllo($codice_voucher, $importo);
    //Attraveso questa Funzione puoi controllare se il buono e' stato speso

    Operazione_Di_Transazione($codice_voucher, $importo);
    //Così Puoi andare a utilizzare un voucher

    Operazione_Di_Impegno($codice_voucher, $importo); //Ancora non disponibile
    //Puoi Impegnare un voucher attraverso questa funzione

    Operazione_Di_Attivazione($codice_voucher);
    //Puoi accreditarti come esercente attraverso questa funzione
