<?php

/*
*  18app PHP SDK
*  Copyright Pasquale De Rose 2017
*
*  This program is free software: you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation, version 3 of the License.
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  You should have received a copy of the GNU General Public License
*  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class app18{
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
  
  protected function Operazione_Errore_01($dati_errore){
    //Implementare metodo
  }
  
  protected function Operazione_Errore_02($dati_errore){
    //Implementare metodo
  }
  
  protected function Operazione_Errore_03($dati_errore){
    //Implementare metodo
  }
  
  protected function Operazione_Errore_04($dati_errore){
    //Implementare metodo
  }
  
  protected function Operazione_Errore_05($dati_errore){
    //Implementare metodo
  }
  
  protected function Operazione_Errore_06($dati_errore){
    //Implementare metodo
  }
  
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
        $result = $this->client->Check($data);
    
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
            
  public final function Operazione_Di_Controllo($codice_voucher, $importo = NULL){
    return $this->Esegui_Operazione(1, $codice_voucher,  $importo);
  }
  
  public final function Operazione_Di_Transazione($codice_voucher, $importo = NULL){
    return $this->Esegui_Operazione(2, $codice_voucher, $importo);
  }
  
  public final function Operazione_Di_Attivazione(){
    return $this->Esegui_Operazione(1, '11aa22bb', NULL);
  }
}

?>
    
    
