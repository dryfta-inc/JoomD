TRUNCATE TABLE `#__joomd_fieldtypes`;

INSERT INTO `#__joomd_fieldtypes` (`id`, `type`, `label`) VALUES
(1, 'textfield', 'TEXTFIELD'),
(2, 'radio', 'RADIO_BUTTON'),
(3, 'checkbox', 'CHECKBOX'),
(4, 'select', 'SELECT_LIST'),
(5, 'textarea', 'TEXTAREA'),
(6, 'wysiwig', 'WYSIWIG'),
(7, 'date', 'DATE'),
(8, 'email', 'EMAIL'),
(9, 'url', 'URL'),
(10, 'image', 'IMAGE'),
(11, 'file', 'FILE'),
(12, 'video', 'VIDEO'),
(13, 'youtube', 'YOUTUBE'),
(14, 'address', 'ADDRESS');


TRUNCATE TABLE `#__joomd_plugintype`;

INSERT INTO `#__joomd_plugintype` (`id`, `name`) VALUES
(1, 'CORE'),
(2, 'ENTRY_SPECIFIC'),
(3, 'CUSTOM_FIELD'),
(4, 'TEMPLATE'),
(5, 'OTHER');