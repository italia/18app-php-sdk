Documentazione Per 18appSDK
===========================

Introduzione
------------

Per utilizzare questa libreria bisogna includere nel progetto il file
**18appSDK.php** attraverso :

    include('18appSDK.php');

E andare a disabilitare la cache WSDL per il client soap :

    ini_set('soap.wsdl_cache_enabled',0); //
    ini_set('soap.wsdl_cache_ttl',0);

Successivamente si deve creare un oggetto di tipo **app18** attraverso :

    $location="Url sito di controllo voucher";
    $certificato="Posizione certificato";
    $pswd="Password del certificato";
    $pi="Partita iva esercente";
    $log_path="Posizione file log";


    $test= new app18($location, $certificato, $pswd, $pi, $log_path);

Metodi
------

Hai appena creato un oggetto. I suoi metodi sono: :

    Operazione_Di_Controllo($codice_voucher);
    //Attraveso questa Funzione puoi controllare se il buono e' stato speso

    Operazione_Di_Transazione_Parziale($codice_voucher, $importo);
    //Così Puoi andare a utilizzare un voucher parzialmente

    Operazione_Di_Transazione_Totale($codice_voucher, $importo);
    //Così Puoi andare a utilizzare un voucher

    Operazione_Di_Impegno($codice_voucher, $importo); //Ancora non disponibile
    //Puoi Impegnare un voucher attraverso questa funzione

    Operazione_Di_Attivazione();
    //Puoi accreditarti come esercente attraverso questa funzione

Gestione Degli errori
---------------------

La linea giuda per esercenti spiega che si possono avere come risposta
dal server 6 tipi di errori le cui azioni devono essere implementate
dallo sviluppatore estendendo la classe app18 e facendo un override
della funzione dell' errore:

    include('18appSDK.php');
    class new_app18 exstends app 18{

        function Operazione_Errore_01($dati_errore){
        /*Implementre metodo
        Errore nel formato dei parametri in input, verificarli e riprovare*/
        }

        function Operazione_Errore_02($dati_errore){
        /*Implementre metodo
        il buono richiesto non è disponibile sul sistema o è già stato riscosso o annullato*/
        }

        function Operazione_Errore_03($dati_errore){
        /*Implementre metodo
        Impossibile attivare l'esercente. Verificare che i dati siano corretti e che l'esercente non sia già stato attivato*/
        }

        function Operazione_Errore_04($dati_errore){
        /*Implementre metodo
        L'importo richiesto è superiore all'importo del buono selezionato*/
        }

        function Operazione_Errore_05($dati_errore){
        /*Implementre metodo
        Non si può verificare o consumare il buono poichè l'esercente risulta non attivo*/
        }

        function Operazione_Errore_06($dati_errore){
        /*Implementre metodo
        Ambito e bene del buono non coincidono con ambiti e beni trattati dall’esercente*/
        }
    }

Linee guida per l'utilizzo
--------------------------

Per utilizzare al meglio questa libreria si consiglia di creeare un
nuovo oggetto ed implementare la classe app18 e fare un override dei
metodi per la gestione degli errori.

Credits
-------

Aloisi Romano A.K.A romixoid

Bruno Vincenzo A.K.A. alvin90

De Rose Pasquale A.K.A J-Lemon

Fortunato Luciano Maria A.K.A flucio88

Pironti Francesco Aurelio A.K.A francescopirox

\*Conversione certificato da P12 a Pem
--------------------------------------

Guida per generare certificato PEM per uso esercenti 18App in ambiente
PHP:

    openssl pkcs12 -in certificatop12.p12 -out certificato.pem
