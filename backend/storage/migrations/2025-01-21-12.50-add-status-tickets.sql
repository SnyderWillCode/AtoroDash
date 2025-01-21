ALTER TABLE `mythicalclient_tickets` ADD `status` ENUM (
	'open',
	'closed',
	'waiting',
	'replied',
	'inprogress'
) NOT NULL DEFAULT 'open' AFTER `priority`;