import createMiddleware from "next-intl/middleware";
import { type NextRequest, NextResponse } from "next/server";
import { routing } from "./i18n/routing";

const handleI18nRouting = createMiddleware(routing);

function decodeRewritePath(response: NextResponse) {
  const rewrite = response.headers.get("x-middleware-rewrite");
  if (!rewrite) return;

  try {
    const decoded = decodeURI(rewrite);
    if (decoded !== rewrite) {
      response.headers.set("x-middleware-rewrite", decoded);
    }
  } catch {
    // Keep the original rewrite path.
  }
}

export default function proxy(request: NextRequest) {
  const response = handleI18nRouting(request);
  decodeRewritePath(response);
  return response;
}

export const config = {
  matcher: ["/((?!api|_next|_vercel|.*\\..*).*)"],
};
