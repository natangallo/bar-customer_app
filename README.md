# Bar Customer Fund Management Web App

## Obiettivo
Questa web app è progettata per i clienti di un bar che desiderano gestire un fondo cassa anticipato. Il bar apre un conto per ciascun cliente, dove vengono tracciate tutte le spese effettuate presso il bar.

## Funzionalità Principali

### Pre-caricamento Dati
Il bar pre-carica nel database i dati dei clienti, inclusi:
- Nome
- Cognome
- Numero di stanza
- Nome del gruppo visitatore di riferimento
- Data della settimana in corso

### Registrazione Cliente
- I clienti si registrano sul portale clienti.
- Durante la registrazione, i clienti accettano un disclaimer che spiega lo scopo dell'app.
- Una volta accettati i termini, i clienti possono scegliere una password personalizzata.

### Conferma del Bar
- Il bar visualizza e conferma l'apertura del conto cliente.
- Fino a quando la conferma non viene conclusa, non è possibile definire l'ammontare della cifra nel fondo.

### Gestione Spese
- Ogni spesa viene segnalata dal bar al momento dell'acquisto.
- Le spese sono visualizzabili anche dal cliente tramite l'app.
- Il cliente può solo visualizzare i prodotti acquistati e il saldo rimanente, senza possibilità di modifica.

### Gestione Prodotti
- Il bar carica e gestisce la lista dei prodotti, suddivisi in gelati e bibite.
- I clienti possono vedere i prodotti acquistati e il loro prezzo.
- Il bar può modificare i prezzi e la disponibilità dei prodotti.

### Tracciamento Attività
- Ogni operazione (ordine cliente, modifica prezzi, ecc.) viene tracciata.
- Una pagina amministrativa elenca tutte le attività con tipo di operazione, data e ora, e profilo che ha eseguito l'operazione.

## Dettagli Tecnici

### Installazione
1. Clonare il repository:
    ```bash
    git clone https://github.com/tuo-utente/tuo-repository.git
    ```
2. Installare le dipendenze:
    ```bash
    cd tuo-repository
    composer install
    npm install
    ```

### Configurazione
- Configurare il file `.env` con i dettagli del database e altre impostazioni necessarie.
- Eseguire le migrazioni del database:
    ```bash
    php artisan migrate
    ```

### Esecuzione
- Avviare il server di sviluppo:
    ```bash
    php artisan serve
    npm run dev
    ```

### Credenziali Admin
- Nome utente: `admin`
- Password: `password` (da cambiare dopo il primo login)

## Contribuzione
1. Fork il progetto.
2. Crea un nuovo branch:
    ```bash
    git checkout -b feature/nome-feature
    ```
3. Effettua le tue modifiche e commit:
    ```bash
    git commit -m "Aggiungi una nuova feature"
    ```
4. Push al branch:
    ```bash
    git push origin feature/nome-feature
    ```
5. Apri una pull request.

## Licenza
Questo progetto è rilasciato sotto la [MIT License](LICENSE).

---

Per ulteriori dettagli, contattare il team di sviluppo.
