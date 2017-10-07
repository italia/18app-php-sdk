Guida per generare certificato PEM per uso esercenti 18App in ambiente PHP

	1) openssl pkcs12 -in pathcertificato.p12 -out newfile.crt.pem -clcerts -nokeys
		inserire la password del certificato P12 quando richiesto

	2) openssl pkcs12 -in pathcertificato.p12 -out newfile.key.pem -nocerts -nodes
		inserire la password del certificato P12 quando richiesto

	3) Una volta ottenuto il certificato e la chiave privata : 
		openssl pkcs12 -in pathcertificato.p12 -out newfile.pem
		quando richiesto creare una nuova passphrase per il certificato appena creato

	4) Conversione effettuata
