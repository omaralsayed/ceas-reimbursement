CREATE TABLE `reimbursement_main` (
  `uid` int(11) NOT NULL,
  `name` text NOT NULL,
  `position` text NOT NULL,
  `email` text NOT NULL,
  `mid` text NOT NULL,
  `date` text NOT NULL,
  `vendor` text NOT NULL,
  `amount` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` text NOT NULL,
  `type` text NOT NULL,
  `receipt_name` text NOT NULL,
  `document_name` text,
  `officer_name` text,
  `officer_position` text,
  `address` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;