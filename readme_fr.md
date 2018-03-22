# Installation
Autre gestion du Portier Doorbird
  
Ce plugin est principalement d�di� � l'utilisation du Doorbird D2101V avec firmware >= 000110 (nouvelle API).  
    
### Les fonctionnalit�s

![STEP0](https://i.imgur.com/uq355za.png)  
  
*Ce plugin installe:*
  
* Un �tat STATUT qui donne l'�tat de la connexion au Doorbird, et fournira le type d'appareil, la r�f�rence du firmware le cas �ch�ant, le nombre de relais utilisables.  
* Un actionneur COMMANDES qui vous permet, depuis eedomus, de r�aliser des actions sur le portier : Allumer la lumi�re, enregistrer un snapshot sur le FTP, ouvrir un relais
* Un �tat EVENTS qui est positionn� par le Doorbird lui-m�me en fonction d'�v�nements (Sonnette, Mouvement, Relais, RFID) r�utilisables dans vos r�gles.  
* Un �tat ANALYTICS qui fournit les statistiques d'utilisation  
  
  
L'�tat EVENTS permet �galement d'initialiser le Doorbird avec les diff�rentes requ�tes d'appels � l'API eedomus (INITIALIZE).    
  
![STEP1](https://i.imgur.com/pp1EPX4.png)  
    
### Pr�requis pour l'image
  
Avant d'installer le plugin, vous pouvez cr�er un nouveau p�riph�rique eedomus de type "Cam�ra - Autre", afin d'y d�poser les captures du Doorbird.  
Depuis "Configuration > Ajouter ou supprimer un p�riph�rique > Ajouter un autre type de cam�ra > Cam�ra - Autre  
et saisissez les donn�es selon l'illustration suivante.  
  
![STEP8](https://i.imgur.com/Jk3isEs.png)   
  
Conserver le login/mdp FTP pour la suite de l'installation.  
NB : Vous ne pourrez pas acc�der au direct.  
  
    
### Installation du plugin
    
*Voici les diff�rents champs � renseigner:*

* [Obligatoire] - Le nom du Portier Doorbird
* [Obligatoire] - L'IP locale du Doorbird
* [Obligatoire] - L'identifiant Doorbird (Utilisateur habilit� de type API-Operator dans l'application Doorbird)
* [Obligatoire] - Le mot de passe associ�
* [Optionnel]   - L'adresse du serveur FTP (par d�faut : camera.eedomus.com)
* [Optionnel]   - Le login pour l'acc�s au serveur FTP
* [Optionnel]   - Le mot de passe pour l'acc�s au serveur FTP
* [Obligatoire] - Votre code API User eedomus
* [Obligatoire] - Votre code API Secret eedomus
  
![STEP3](https://i.imgur.com/TJHRlt9.png)  
  
  
### Param�trage RFID
  
Si vous utilisez des badges RFID,  
apr�s les avoir enregistr�s dans l'application smarpthone Doorbird, r�cup�rez les diff�rents codes de transpondeur afin de param�trer le capteur "EVENTS" eedomus.  
  
Entrez dans la configuration du capteur "EVENTS", et affichez les valeurs masqu�es.  
Pour les diff�rents RFID utilis�s, saisissez le code du transpondeur entre parenth�ses, � la place de la mention "transponder_id".  
Apr�s les parenth�ses, vous pouvez �crire le nom du titulaire du badge par exemple, pour vous aider dans vos futures r�gles.  
Puis enregistrez les modifications.  
   
![STEP2](https://i.imgur.com/8zT6UNl.png) 
  
  
### Premi�re Utilisation
  
Si les donn�es de connexion sont correctes, le capteur STATUT vous confirmera le type d'appareil, le firmware associ� et le nombre de relais autoris�s (le polling du statut est � 30 mn).  
Le nombre de relais autoris� d�pend du param�trage du portier dans l'application Doorbird : vous pouvez d�sactiver l'utilisation de certains relais.  
En cas de probl�me de connexion, le capteur vous indiquera "Doorbird Connection Error".  
  
Pour vous en assurer, lancez l'action "SnapshotFTP" du p�riph�rique "COMMANDES" pour afficher une premi�re capture dans l'image Cam�ra Doorbird FTP.
  
Si tout est OK, et une fois le param�trage RFID r�alis�, lancez l'action "INITIALIZE" sur l'actionneur "EVENTS" depuis l'interface eedomus :  
  
![STEP4](https://i.imgur.com/fQkFFx0.png) 
  
Cette action cr�era l'ensemble des "Favorites" pour le Doorbird (="Visite HTTP" dans l'application smartphone Doorbird en fran�ais).  
Il s'agit des diff�rentes requ�tes HTTP vers l'API eedomus permettant de positionner le capteur "EVENTS" sur l'�v�nement associ� du Doorbird.  
    
Vous retrouverez donc ensuite ces diff�rentes requ�tes http dans le menu "Favoris -> Visite HTTP" du menu Administration de l'application :  
  
![STEP5](https://i.imgur.com/ywLtqq2.jpg) 
  
  
En m�me temps, cela cr�era le param�trage de base suivant pour les �v�nements dans votre application Doorbird (Schedules) :  
  
* Pour la sonnette : Notification smartphone + Appel HTTP eedomus #1 (Doorbell)  
* Pour un mouvement : Appel HTTP eedomus #2 (Motion)  
* Pour chacun des relais autoris� : Appel HTTP eedomus #1x (Relay)  
* Pour un RFID : Activation du relais n�1 + Appel HTTP eedomus #2x (RFID)  
  
Voici par exemple ce que vous retrouverez dans l'application pour le "Calendrier Sonnette" du menu d'Administration :
  
* Une notification push activ�e quels que soient le jour de la semaine et l'heure  
* Un appel HTTP au favoris "Doorbell" quels que soient le jour de la semaine et l'heure  
  
![STEP6](https://i.imgur.com/TBm8k8A.png) 
![STEP7](https://i.imgur.com/UYtgNuX.png)  
  
Libre � vous ensuite, depuis l'application Doorbird, de modifier les param�tres de jour et d'horaires pour activer ou non le fonctionnement des notifications et de l'appel HTTP li�s � la sonnette...  
  
NB : Si vous modifiez les calendriers (Schedules) depuis l'application, un nouveau lancement d'INITIALIZE de l'actionneur EVENTS depuis l'interface eedomus viendra r�initialiser tous les param�tres au param�trage de base ci-dessus.  
  

Influman 2018  
therealinfluman@gmail.com  
[Paypal Me](https://www.paypal.me/influman "paypal.me")  


  
   

  
   
   
  



 

 

  


