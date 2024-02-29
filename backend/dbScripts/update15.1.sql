ALTER TABLE MATERIEL_ELEMENT ADD numeroSerie TEXT NULL AFTER peremptionNotification;
ALTER TABLE RESERVES_MATERIEL ADD numeroSerie TEXT NULL AFTER peremptionNotificationReserve;

UPDATE CONFIG SET version = '15.1';