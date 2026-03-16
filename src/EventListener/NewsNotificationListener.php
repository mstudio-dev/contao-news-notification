<?php

declare(strict_types=1);

namespace Mstudio\ContaoNewsNotificationBundle\EventListener;

use Contao\BackendUser;
use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\Mailer\MailerInterface;
use Contao\DataContainer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Email;

#[AsCallback(table: 'tl_news', target: 'config.onsubmit_callback')]
class NewsNotificationListener
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly MailerInterface $mailer,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(DataContainer $dc): void
    {
        $this->framework->initialize();

        if (!$dc->activeRecord) {
            $this->logger->warning('NewsNotification: No activeRecord available.');

            return;
        }

        if (!Config::get('mstudio_news_notification_enabled')) {
            return;
        }

        if (empty($dc->activeRecord->teaser)) {
            return;
        }

        $recipient = Config::get('mstudio_news_notification_recipient');

        if (!$recipient) {
            $this->logger->warning('NewsNotification: No recipient configured – aborting.');

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
            $this->logger->info(sprintf('NewsNotification: Email sent successfully to "%s".', $recipient));
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('NewsNotification: Failed to send email – %s', $e->getMessage()));
        }
    }
}
