# Documentazione

## Introduzione
Questo progetto si tratta di un eseperimento, sotto diversi punti di vista. <br>
Il risultato finale rappresenta un'applicazione che permette di registrare più viaggi. Per ogni viaggio viene automaticamente generata una pagina con tutti i giorni del viaggio. Per ogni giorno si possono inserire le tappe del giorno. È possibile inserire delle immagini in ogni tappa, e fornendo le coordinate (latitudine e longitudine) verrano mostrate su di una mappa tramite dei marker. <br>
Per creare l'applicazione ho utilizzato Vue 3 e Laravel contemporaneamente, cercando di sfruttare la potenzialità del pattern MVC di Laravel, in combinazione con la dinamicità nella creazione delle viste, tramite i componenti di Vue. 

## Frontend

Il frontend è gestito tramite componenti Vue, sfruttando la Option API. Questi componenti vengono montati all'interno di app.js direttametne su #app, quindi vengono utilizzati direttamente all'interno delle rispettive Viste Blade. I componenti ricevono i dati dalla vista tramite props e sfruttano i dati per generare tramite la Option API, lo stile (scoped), la logica e la visualizzazione in pagina. <br>

## Backend
Per il backend viene utilizzato Laravel. Essendo un'applicazione creata per uso personale, sfrutta il pacchetto di Larvel/Breeze per l'autenticazione. Come già accennato, viene sfruttato il pattern MVC tipico di Laravel. Passando per le rotte di web.php vengono quindi effettuate chiamate ai Controller che manipolano i dati, sia per l'inserimento degli stessi nel Database e nell'online Storage, sia per rifinirli per le viste. <br>


## Altro
### Servizi Esterni
- **JawsDB** => Database
- **Firebase** => File Storage
- **Heroku** => Hosting

### Tecnologie Utilizzate
- **Vite**
- **Vue.js**
- **Laravel**
- **Bootstrap**
- **SCSS(Sass)**

### Linguaggi Utilizzati
- **HTML**
- **CSS**
- **JavaScript**
- **PHP**

### Strumenti di Sviluppo
- **npm** => Gestione pacchetti e strumenti di build per il frontend
- **composer** => Gestione delle dipendenze PHP per il backend
- **php artisan** => Comandi CLI per Laravel
