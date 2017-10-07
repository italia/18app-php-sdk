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

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
ini_set('soap.wsdl_cache_enabled',0);
ini_set('soap.wsdl_cache_ttl',0);
error_reporting(-1);

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
                  "\n\t\tException Message: " . $e->detail->FaultVoucher->exceptionMessage . "\n\n", 3, $log_path);
      }
  
    return $output;
  }
            
  public function Operazione_Di_Controllo($codice_voucher, $importo = NULL){
    return $this->Esegui_Operazione(1, $codice_voucher,  $importo);
  }
  
  public function Operazione_Di_Transazione($codice_voucher, $importo = NULL){
    return $this->Esegui_Operazione(2, $codice_voucher, $importo);
  }
  
  public function Operazione_Di_Impegno($codice_voucher, $importo = NULL){
    return $this->Esegui_Operazione(3, $codice_voucher, $importo);
  }
  
  public function Operazione_Di_Attivazione($codice_voucher){
    return $this->Esegui_Operazione(1, $codice_voucher, NULL);
  }
  
}

?>
    
    
