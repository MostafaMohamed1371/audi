"use client";

import { Button } from "@/app/components/ui/button";
import { subscribeNewsletter } from "@/lib/api";
import { cn } from "@/lib/utils";
import { useLocale, useTranslations } from "next-intl";
import { useState } from "react";

type Props = {
  isRtl: boolean;
};

export function FooterNewsletterForm({ isRtl }: Props) {
  const t = useTranslations("common.footer.newsletter");
  const locale = useLocale();
  const [email, setEmail] = useState("");
  const [status, setStatus] = useState<"idle" | "success" | "error">("idle");
  const [isSubmitting, setIsSubmitting] = useState(false);

  async function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setIsSubmitting(true);
    setStatus("idle");

    try {
      await subscribeNewsletter({ email, locale }, locale);
      setStatus("success");
      setEmail("");
    } catch {
      setStatus("error");
    } finally {
      setIsSubmitting(false);
    }
  }

  return (
    <div dir={isRtl ? "rtl" : "ltr"} className="space-y-3">
      <p className="text-sm font-medium text-white">{t("title")}</p>
      <form onSubmit={handleSubmit} className="flex flex-col gap-2 sm:flex-row">
        <input
          type="email"
          value={email}
          onChange={(event) => setEmail(event.target.value)}
          placeholder={t("placeholder")}
          required
          className="min-w-0 flex-1 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm text-white placeholder:text-white/60 outline-none focus:border-white/40"
        />
        <Button
          type="submit"
          disabled={isSubmitting}
          className={cn(
            "h-10 shrink-0 rounded-full bg-white px-5 text-sm font-medium text-primary hover:bg-white/90",
          )}
        >
          {t("submit")}
        </Button>
      </form>
      {status === "success" ? (
        <p className="text-xs text-white/80">{t("success")}</p>
      ) : null}
      {status === "error" ? (
        <p className="text-xs text-red-200">{t("error")}</p>
      ) : null}
    </div>
  );
}
