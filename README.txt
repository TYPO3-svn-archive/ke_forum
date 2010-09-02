Include
alle static Templates vom Forum einbinden

Constants

styles.content.loginform.pid = 24

tx_keforum.pid.categories=21
tx_keforum.pid.threads=22
tx_keforum.pid.posts=23
tx_keforum.pid.search=32
tx_keforum.pid.moderate=33
tx_keforum.pid.profile=20

tx_keforum.mail.debug.mailtext =1
tx_keforum.mail.debug.overwriteRecipient =1
tx_keforum.mail.debug.overwriteRecipient.email =chris.sander@mac.com

tx_keforum.teaser.count=5
tx_keforum.teaser.maxChars=30

tx_keforum.date.format.default=%e.%B %Y, %H:%M Uhr 
tx_keforum.date.format.short=%e.%m %Y, %H:%M
tx_keforum.date.format.member=%d.%B.%Y
tx_keforum.date.format.teaser=%d.%m.%Y

tx_keforum.mail.sender_name=Sender
tx_keforum.mail.sender_email=noreplay@forum.de
tx_keforum.mail.forum_name=Mein Forum
tx_keforum.mail.footer=Ihr Forenteam
tx_keforum.mail.subject.notify_single=Neue Nachricht im Forum
tx_keforum.mail.subject.notify_cron=Betreff Cron
tx_keforum.mail.subject.notify_moderator=Neuer Beitrag im Forum
tx_keforum.mail.interval=1440
tx_keforum.mail.mailsPerRequest =2

tx_keforum.mail.mailsPerRequest =2

tx_keforum.pagination.itemsPerPage = 5
tx_keforum.pagination.itemsInBrowser = 3



Setup

config{
	metaCharset = utf-8
	htmlTag_langKey = de-DE
	locale_all = de_DE
	language = de
	sys_language_uid = 0
	no_cache = true
}

# Default PAGE object:
page = PAGE
page.includeCSS.css=typo3conf/ext/ke_forum/res/css/ke_forum.css


page.5=TEXT
page.5.value=<div class="tx-keforum-pi1">
page.10 < styles.content.get
page.15=TEXT
page.15.value=</div>

plugin.tx_keforum.controller_categories.pid.categories = {$tx_keforum.pid.categories}
plugin.tx_keforum.controller_categories.pid.threads = {$tx_keforum.pid.threads}
plugin.tx_keforum.controller_categories.pid.posts = {$tx_keforum.pid.posts}
plugin.tx_keforum.controller_categories.pid.search = {$tx_keforum.pid.search}
plugin.tx_keforum.controller_categories.date.format.default = {$tx_keforum.date.format.default}

plugin.tx_keforum.controller_teaser.pid.categories = {$tx_keforum.pid.categories}
plugin.tx_keforum.controller_teaser.pid.threads = {$tx_keforum.pid.threads}
plugin.tx_keforum.controller_teaser.pid.posts = {$tx_keforum.pid.posts}
plugin.tx_keforum.controller_teaser.pid.search = {$tx_keforum.pid.search}
plugin.tx_keforum.controller_teaser.date.format.default = {$tx_keforum.date.format.teaser}
plugin.tx_keforum.controller_teaser.count={$tx_keforum.teaser.count}
plugin.tx_keforum.controller_teaser.maxChars={$tx_keforum.teaser.maxChars}

plugin.tx_keforum.controller_threads.pid.categories = {$tx_keforum.pid.categories}
plugin.tx_keforum.controller_threads.pid.threads = {$tx_keforum.pid.threads}
plugin.tx_keforum.controller_threads.pid.posts = {$tx_keforum.pid.posts}
plugin.tx_keforum.controller_threads.pid.search = {$tx_keforum.pid.search}
plugin.tx_keforum.controller_threads.date.format.default = {$tx_keforum.date.format.default}
plugin.tx_keforum.controller_threads.pagination.itemsPerPage = {$tx_keforum.pagination.itemsPerPage}
plugin.tx_keforum.controller_threads.pagination.itemsInBrowser = {$tx_keforum.pagination.itemsInBrowser}
plugin.tx_keforum.controller_threads.orderBy= crdate DESC
plugin.tx_keforum.controller_threads.mail.sender_name={$tx_keforum.mail.sender_name}
plugin.tx_keforum.controller_threads.mail.sender_email={$tx_keforum.mail.sender_email}
plugin.tx_keforum.controller_threads.mail.forum_name={$tx_keforum.mail.forum_name}
plugin.tx_keforum.controller_threads.mail.footer={$tx_keforum.mail.footer}
plugin.tx_keforum.controller_threads.mail.subject.notify_single={$tx_keforum.mail.subject.notify_single}
plugin.tx_keforum.controller_threads.mail.subject.notify_cron={$tx_keforum.mail.subject.notify_cron}
plugin.tx_keforum.controller_threads.mail.subject.notify_moderator={$tx_keforum.mail.subject.notify_moderator}


plugin.tx_keforum.controller_posts.pid.categories = {$tx_keforum.pid.categories}
plugin.tx_keforum.controller_posts.pid.threads = {$tx_keforum.pid.threads}
plugin.tx_keforum.controller_posts.pid.posts = {$tx_keforum.pid.posts}
plugin.tx_keforum.controller_posts.pid.profile = {$tx_keforum.pid.profile}
plugin.tx_keforum.controller_posts.pid.moderate = {$tx_keforum.pid.moderate}
plugin.tx_keforum.controller_posts.date.format.default = {$tx_keforum.date.format.default}
plugin.tx_keforum.controller_posts.date.format.member = {$tx_keforum.date.format.member}
plugin.tx_keforum.controller_posts.pagination.itemsPerPage = {$tx_keforum.pagination.itemsPerPage}
plugin.tx_keforum.controller_posts.pagination.itemsInBrowser = {$tx_keforum.pagination.itemsInBrowser}
plugin.tx_keforum.controller_posts.orderBy= crdate ASC
plugin.tx_keforum.controller_posts.mail.sender_name={$tx_keforum.mail.sender_name}
plugin.tx_keforum.controller_posts.mail.sender_email={$tx_keforum.mail.sender_email}
plugin.tx_keforum.controller_posts.mail.forum_name={$tx_keforum.mail.forum_name}
plugin.tx_keforum.controller_posts.mail.footer={$tx_keforum.mail.footer}
plugin.tx_keforum.controller_posts.mail.subject.notify_single={$tx_keforum.mail.subject.notify_single}
plugin.tx_keforum.controller_posts.mail.subject.notify_cron={$tx_keforum.mail.subject.notify_cron}
plugin.tx_keforum.controller_posts.mail.subject.notify_moderator={$tx_keforum.mail.subject.notify_moderator}

plugin.tx_keforum.controller_search.pid.categories = {$tx_keforum.pid.categories}
plugin.tx_keforum.controller_search.pid.threads = {$tx_keforum.pid.threads}
plugin.tx_keforum.controller_search.pid.posts = {$tx_keforum.pid.posts}
plugin.tx_keforum.controller_search.date.format.default = {$tx_keforum.date.format.default}
plugin.tx_keforum.controller_search.date.format.member = {$tx_keforum.date.format.member}
plugin.tx_keforum.controller_search.date.format.short = {$tx_keforum.date.format.short}
plugin.tx_keforum.controller_search.pagination.itemsPerPage = {$tx_keforum.pagination.itemsPerPage}
plugin.tx_keforum.controller_search.pagination.itemsInBrowser = {$tx_keforum.pagination.itemsInBrowser}

plugin.tx_keforum.controller_moderator.pid.categories = {$tx_keforum.pid.categories}
plugin.tx_keforum.controller_moderator.pid.threads = {$tx_keforum.pid.threads}
plugin.tx_keforum.controller_moderator.pid.posts = {$tx_keforum.pid.posts}
plugin.tx_keforum.controller_moderator.pid.profile = {$tx_keforum.pid.profile}
plugin.tx_keforum.controller_moderator.pid.moderate = {$tx_keforum.pid.moderate}
plugin.tx_keforum.controller_moderator.date.format.default = {$tx_keforum.date.format.default}
plugin.tx_keforum.controller_moderator.date.format.member = {$tx_keforum.date.format.member}
plugin.tx_keforum.controller_moderator.mail.sender_name={$tx_keforum.mail.sender_name}
plugin.tx_keforum.controller_moderator.mail.sender_email={$tx_keforum.mail.sender_email}
plugin.tx_keforum.controller_moderator.mail.forum_name={$tx_keforum.mail.forum_name}
plugin.tx_keforum.controller_moderator.mail.footer={$tx_keforum.mail.footer}
plugin.tx_keforum.controller_moderator.mail.subject.notify_single={$tx_keforum.mail.subject.notify_single}
plugin.tx_keforum.controller_moderator.mail.subject.notify_cron={$tx_keforum.mail.subject.notify_cron}
plugin.tx_keforum.controller_moderator.mail.subject.notify_moderator={$tx_keforum.mail.subject.notify_moderator}

plugin.tx_keforum.controller_cron.pid.categories = {$tx_keforum.pid.categories}
plugin.tx_keforum.controller_cron.pid.threads = {$tx_keforum.pid.threads}
plugin.tx_keforum.controller_cron.pid.posts = {$tx_keforum.pid.posts}
plugin.tx_keforum.controller_cron.pid.profile = {$tx_keforum.pid.profile}
plugin.tx_keforum.controller_cron.date.format.default = {$tx_keforum.date.format.default}
plugin.tx_keforum.controller_cron.date.format.member = {$tx_keforum.date.format.member}
plugin.tx_keforum.controller_cron.mail.sender_name={$tx_keforum.mail.sender_name}
plugin.tx_keforum.controller_cron.mail.sender_email={$tx_keforum.mail.sender_email}
plugin.tx_keforum.controller_cron.mail.forum_name={$tx_keforum.mail.forum_name}
plugin.tx_keforum.controller_cron.mail.footer={$tx_keforum.mail.footer}
plugin.tx_keforum.controller_cron.mail.subject.notify_single={$tx_keforum.mail.subject.notify_single}
plugin.tx_keforum.controller_cron.mail.subject.notify_cron={$tx_keforum.mail.subject.notify_cron}
plugin.tx_keforum.controller_cron.mail.subject.notify_moderator={$tx_keforum.mail.subject.notify_moderator}
plugin.tx_keforum.controller_cron.interval= {$tx_keforum.mail.interval}
plugin.tx_keforum.controller_cron.mailsPerRequest= {$tx_keforum.mail.mailsPerRequest}
plugin.tx_keforum.controller_cron.mail.debug.mailtext= {$tx_keforum.mail.debug.mailtext}
plugin.tx_keforum.controller_cron.mail.debug.overwriteRecipient = {$tx_keforum.mail.debug.overwriteRecipient}
plugin.tx_keforum.controller_cron.mail.debug.overwriteRecipient.email= {$tx_keforum.mail.debug.overwriteRecipient.email}


