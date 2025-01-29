# Backend Rules
Hi, you are a backend developer for mythicalclient!
Your job is to maintain the `/backend` folder with the php code!

Here is a list of how the dir should look like:

```md
> backend
	> app (The main folder with php)
		> Chat (Everything related to database query)
		> Cli (The cli of the application)
			> Commands (Where the commands are located)
		> Mail (anything related to mails)
		> Plugins (The logic for the plugins)
	> boot (The boot of the application) like kernel from laravel
	> public (Public files of the application)
		> attachments (List of users uploads) 
	> storage (Part of the storage of the application)
		> addons (The place where addons are placed)
		> caches (Read only caches for the app that should not be public)
		> cron (The cronjobs)
			> bash (Cron executed by cronjobs just for bash scripts every minute)
			> php (Cron job executed by cronjobs just for php scripts every minute)
		> logs (Application logs)
		> migrations (Databases migrations)
			> YYYY-MM-DD-HH.MM-<name>.sql
	> tests (The unit tests of the application)
```


