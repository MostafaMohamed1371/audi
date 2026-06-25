import { getRequestConfig } from 'next-intl/server';
import { routing } from './routing';

export default getRequestConfig(async ({ requestLocale }) => {
  let locale = await requestLocale;

  if (!locale || !routing.locales.includes(locale as any)) {
    locale = routing.defaultLocale;
  }

  const messages = {
    common: (await import(`../messages/${locale}/common.json`)).default,
    home: (await import(`../messages/${locale}/home.json`)).default,
    about: (await import(`../messages/${locale}/about.json`)).default,
    contact: (await import(`../messages/${locale}/contact.json`)).default,
    strategy: (await import(`../messages/${locale}/strategy.json`)).default,
    programs: (await import(`../messages/${locale}/programs.json`)).default,
    media: (await import(`../messages/${locale}/media.json`)).default,
    resources: (await import(`../messages/${locale}/resources.json`)).default,
    faq: (await import(`../messages/${locale}/faq.json`)).default,
    careers: (await import(`../messages/${locale}/careers.json`)).default,
    legal: (await import(`../messages/${locale}/legal.json`)).default,
  };

  return {
    locale,
    messages,
  };
});
