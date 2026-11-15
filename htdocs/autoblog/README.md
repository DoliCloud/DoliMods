# AutoBlog for [Dolibarr ERP & CRM](https://www.dolibarr.org)

Generate the blog articles of your websites (Website module) with AI (AI module), then review and publish them.

## Features

- A queue of **blog topics** (title, keywords, specific instructions, website, language, date not before).
- A **scheduled job** (*AutoBlogGenerateArticles*, disabled by default) that generates the next topics in queue with the AI service configured in the AI module.
- The generated article is stored in the topic: review it, **correct** it (add instructions, the article is generated again), then **publish** it as an online blog post of the website.
- An **editorial setup per website** (description, what sets you apart, target audience, target location, generic instructions...) sent to the AI with each article.

## Prerequisites

- Dolibarr 25+, with the modules Website and AI enabled and an AI service configured.
- PHP 8.1+.

## Licenses

GPLv3 or (at your option) any later version. See file COPYING for more information.
