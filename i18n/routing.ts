import { createNavigation } from 'next-intl/navigation';
import { defineRouting } from 'next-intl/routing';
import type { ComponentProps } from 'react';
import { pathnames } from './pathnames';

export const routing = defineRouting({
  locales: ['ar', 'en'],
  defaultLocale: 'ar',
  localePrefix: 'always',
  localeDetection: false,
  pathnames,
});

export const { Link, redirect, usePathname, useRouter } =
  createNavigation(routing);

export type AppHref = ComponentProps<typeof Link>['href'];
