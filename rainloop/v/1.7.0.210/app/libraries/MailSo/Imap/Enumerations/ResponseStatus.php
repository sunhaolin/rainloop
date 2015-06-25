<?php

/*
 * This file is part of MailSo.
 *
 * (c) 2014 Usenko Timur
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MailSo\Imap\Enumerations;

/**
 * @category MailSo
 * @package Imap
 * @subpackage Enumerations
 */
class ResponseStatus
{
	const OK = 'OK';
	const NO = 'NO';
	const BAD = 'BAD';
	const BYE = 'BYE';
	const PREAUTH = 'PREAUTH';
}
