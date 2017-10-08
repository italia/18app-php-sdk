<?php

/*
*  18app PHP SDK
*  Copyright Pasquale De Rose 2017
*
*  This program is free software: you can redistribute it and/or modify
*  it under the terms of the GNU Lesser Public License as published by
*  the Free Software Foundation; either version 3 of the License, or
*  (at your option) any later version.
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU Lesser Public License for more details.
*
*  You should have received a copy of the GNU Lesser Public License
*  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * 
 */
class app18{
    /**
     * Costruttore della classe app18
     * 
     * @param string $location_url Url del webServer SOAP
     * @param string $certificato_ssl Url del certificato dell'esercente
     * @param string $passphrase Password del certificato dell'esercente
     * @param string $wdsl_url Url del WDSL
     * @param string $pi_esercente Partita IVA dell'esercente
     * @param string $log_path Url del file di log
     * @return NULL
     */
  function __construct($location_url, $certificato_ssl, $passphrase, $wdsl_url, $pi_esercente, $log_path) {
    
    $options = array(
    'location'      => $location_url,
    'local_cert'    => $certificato_ssl,
    'passphrase'    => $passphrase,
    'stream_context'=> stream_context_create(array('ssl'=> array(
                   'verify_peer'=>false,
                   'verify_peer_name'=>false,
                   'allow_self_signed' => true
              )))
    );
    
    $this->pi_esercente = $pi_esercente;
    $this->log_path     = $log_path;
    $this->client       = new SoapClient($wdsl_url, $options);
  }
  
  /**
   * Operazione da implementare in caso di errore nel formato dei parametri in input
   */
  protected function Operazione_Errore_01($dati_errore){
    //Implementare metodo nell'app
  }
  
  /**
   * Operazione da implementare in caso di buono non disponibile sul sistema poichè
   * è stato già riscosso oppure annullato
   */
  protected function Operazione_Errore_02($dati_errore){
    //Implementare metodo nell'app
  }
  
  /**
   * Operazione da implementare in caso di impossibilità ad attivare l'esercente.
   * Dati non corretti o esercente già attivato
   */
  protected function Operazione_Errore_03($dati_errore){
    //Implementare metodo nell'app
  }
  
  /**
   * Operazione da implementare in caso di importo richiesto superiore all'importo
   * del buono selezionato
   */
  protected function Operazione_Errore_04($dati_errore){
    //Implementare metodo
  }
  
  /**
   * Operazione da implementare in caso di impossibilità a verificare o a consumare 
   * il buono poichè l'esercente non risulta attivo
   */
  protected function Operazione_Errore_05($dati_errore){
    //Implementare metodo
  }
  
  /**
   * Operazione da implementare in caso di ambito e bene del buono non coincidenti
   * con ambiti e beni trattati dall'esercente
   */
  protected function Operazione_Errore_06($dati_errore){
    //Implementare metodo
  }
  
  /*
   * Funzione che ricevuti i dati di errori chiama le varie operazioni in base 
   * all'errore ricevuto. 
   * 
   * 
   * @return NULL
   **/
  private function Chiama_Operazione_Errore($dati_errore){
    switch ($dati_errore['info_esito']['codice']) {
      case '01':
        $this->Operazione_Errore_01($dati_errore);
        break;
      case '02':
        $this->Operazione_Errore_02($dati_errore);
        break;
      case '03':
        $this->Operazione_Errore_03($dati_errore);
        break;
      case '04':
        $this->Operazione_Errore_04($dati_errore);
        break;
      case '05':
        $this->Operazione_Errore_05($dati_errore);
        break;
      case '06':
        $this->Operazione_Errore_06($dati_errore);
        break;
    }
  }
  
  /**
   * 
   * @param int $operazione Identificatore operazione da seguire
   * @param string $codice_voucher Codice voucher 18App
   * @param int $importo Importo dell'operazione
   * @return array : Primo elemento positivo in caso di operazione eseguita con 
   *                 successo oppure negativo in caso contrario
   *                 Altri elementi che descrivono l'esito dell'operazione
   *                
   */
  private function Esegui_Operazione($operazione, $codice_voucher, $importo = NULL){
    $data = array(
      'checkReq' => array(
          'tipoOperazione' => $operazione,
          'codiceVoucher' => $codice_voucher,
          'partitaIvaEsercente' => $this->pi_esercente,
          'importo' => $importo
          )
      );
      
      $output = array(
        'esito' => NULL,
        'info_esito' => NULL,
      );
      
      error_log("\nOperazione in data:" . date('l jS \of F Y h:i:s A')        .
                "\n\tDati operazione:\n"                                      . 
                "\t\tTipo Operazione: " . $data['checkReq']['tipoOperazione'] .
                "\n\t\tVoucher: " . $data['checkReq']['codiceVoucher']        . 
                "\n\t\tPI: " . $data['checkReq']['partitaIvaEsercente']       .
                "\n\t\tImporto: " . $data['checkReq']['importo'] . "\n", 3, $this->log_path);
                
      try{
        if(($operazione == 1 && $importo == NULL) || $operazione == 2){
          $result = $this->client->Check($data);
        }else{
          $result = $this->client->Confirm($data);
        }
    
        error_log("\nRisultato operazione:" . 
                  "\n\t\tNominativo Beneficiario: ". $result->checkResp->nominativoBeneficiario .
                  "\n\t\PI: " . $result->checkResp->partitaIvaEsercente                         .
                  "\n\t\tAmbito: " . $result->checkResp->ambito                                 . 
                  "\n\t\Bene: " . $result->checkResp->bene                                      .
                  "\n\t\tImporto: " . $result->checkResp->importo . "\n", 3, $this->log_path);
        
        $output['esito']      = 'positivo';
        $output['info_esito'] = $result;
        
      }catch(\SoapFault $e){
        $output['esito']      = 'negativo';
        $output['info_esito'] = array('codice' => $e->detail->FaultVoucher->exceptionCode,
                                      'errore' => $e->detail->FaultVoucher->exceptionMessage);
        
        error_log("\n\tDettagli Errore:\n" .
                  "\n\t\tException code: " . $e->detail->FaultVoucher->exceptionCode .
                  "\n\t\tException Message: " . $e->detail->FaultVoucher->exceptionMessage . "\n\n", 3, $this->log_path);
        
        $this->Chiama_Operazione_Errore($output);
      }
  
    return $output;
  }
            
  /**
   * Funziona da richiamare per verificare l'importo di un voucher
   * @param string $codice_voucher Codice del voucher da verificare
   * @param float $importo Importo da verificare 
   * @return array : Primo elemento positivo in caso di operazione eseguita con 
   *                 successo oppure negativo in caso contrario
   *                 Altri elementi che descrivono l'esito dell'operazione
   */
  public final function Operazione_Di_Controllo($codice_voucher){
    return $this->Esegui_Operazione(1, $codice_voucher,  NULL);
  }
  
  /**
   * 
   * Funzione da richiamare per effettuare una transazione dall'importo parziale
   * @param string $codice_voucher Codice del voucher da validare
   * @param float $importo Importo da scalare 
   * @return array : Primo elemento positivo in caso di operazione eseguita con 
   *                 successo oppure negativo in caso contrario
   *                 Altri elementi che descrivono l'esito dell'operazione
   */
  public final function Operazione_Di_Transazione_Parziale($codice_voucher, $importo){
    return $this->Esegui_Operazione(1, $codice_voucher, $importo);
  }
  
  /**
   * 
   * Funzione da richiamare per effettuare una transazione del totale dell'importo
   * @param string $codice_voucher Codice del voucher da validare
   * @param float $importo Importo da scalare 
   * @return array : Primo elemento positivo in caso di operazione eseguita con 
   *                 successo oppure negativo in caso contrario
   *                 Altri elementi che descrivono l'esito dell'operazione
   */
  public final function Operazione_Di_Transazione_Totale($codice_voucher){
    return $this->Esegui_Operazione(2, $codice_voucher, NULL);
  }
  
  /**
   *Operazione da richiamare per attivare un esercente 
   *  @return array : Primo elemento positivo in caso di operazione eseguita con 
   *                 successo oppure negativo in caso contrario
   *                 Altri elementi che descrivono l'esito dell'operazione
   * 
   */
  public final function Operazione_Di_Attivazione(){
    return $this->Esegui_Operazione(1, '11aa22bb', NULL);
  }
}
