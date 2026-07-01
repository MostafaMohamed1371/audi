import type { NextConfig } from "next";
import createNextIntlPlugin from "next-intl/plugin";

const withNextIntl = createNextIntlPlugin("./i18n/request.ts");

function apiImageHost(): string | undefined {
  const apiUrl = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000";
  try {
    return new URL(apiUrl).hostname;
  } catch {
    return undefined;
  }
}

const apiHost = apiImageHost();

const nextConfig: NextConfig = {
  images: {
    remotePatterns: [
      ...(apiHost
        ? [
            {
              protocol: "http" as const,
              hostname: apiHost,
              pathname: "/storage/**",
            },
            {
              protocol: "https" as const,
              hostname: apiHost,
              pathname: "/storage/**",
            },
          ]
        : []),
      {
        protocol: "http",
        hostname: "localhost",
        pathname: "/storage/**",
      },
      {
        protocol: "https",
        hostname: "localhost",
        pathname: "/storage/**",
      },
    ],
  },
};

export default withNextIntl(nextConfig);
