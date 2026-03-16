<?php

declare(strict_types=1);

namespace Mstudio\ContaoNewsNotificationBundle\EventListener;

use Contao\BackendUser;
use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Mailer\ContaoMailer;
use Contao\DataContainer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Email;

#[AsCallback(table: 'tl_news', target: 'config.onsubmit_callback')]
class NewsNotificationListener
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ContaoMailer $mailer,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(DataContainer $dc): void
    {
        $this->framework->initialize();

        if (!$dc->activeRecord) {
            $this->logger->warning('NewsNotification: Kein activeRecord vorhanden.');

            return;
        }

        if (empty($dc->activeRecord->teaser)) {
            return;
        }

        $recipient = Config::get('mstudio_news_notification_recipient');

        if (!$recipient) {
            $this->logger->warning('NewsNotification: Kein Empfänger konfiguriert – Versand abgebrochen.');

            return;
        }

        $host = $this->requestStack->getCurrentRequest()?->getHost() ?? 'localhost';
        $sender = Config::get('mstudio_news_notification_sender') ?: ('no-reply@'.$host);

        $username = $GLOBALS['TL_LANG']['MSC']['mstudio_news_notification_unknown_user'];
        $backendUser = BackendUser::getInstance();

        if ($backendUser->id) {
            $username = sprintf('%s (%s)', $backendUser->name, $backendUser->username);
        }

        $body = sprintf(
            $GLOBALS['TL_LANG']['MSC']['mstudio_news_notification_email_body'],
            $dc->activeRecord->headline,
            $dc->activeRecord->alias,
            $dc->activeRecord->id,
            date((string) Config::get('datimFormat')),
            $username,
        );

        $subject = $GLOBALS['TL_LANG']['MSC']['mstudio_news_notification_email_subject'];

        $email = (new Email())
            ->from($sender)
            ->to($recipient)
            ->subject($subject)
            ->text($body);

        try {
            $this->mailer->send($email);
            $this->logger->info(sprintf('NewsNotification: E-Mail erfolgreich an "%s" versendet.', $recipient));
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('NewsNotification: Versand fehlgeschlagen – %s', $e->getMessage()));
        }
    }
}
