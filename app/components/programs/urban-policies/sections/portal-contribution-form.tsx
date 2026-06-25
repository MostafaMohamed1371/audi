"use client";

import { Button } from "@/app/components/ui/button";
import { submitPortalContribution } from "@/lib/api";
import { cn } from "@/lib/utils";
import { Send } from "lucide-react";
import { useLocale, useTranslations } from "next-intl";
import { useState } from "react";

type ContributionType = "publications" | "cities" | "organizations";

type Props = {
  isRtl: boolean;
  defaultType?: ContributionType;
  onSuccess?: () => void;
};

export function PortalContributionForm({
  isRtl,
  defaultType = "publications",
  onSuccess,
}: Props) {
  const t = useTranslations("programs.urbanPolicies.developmentPortal.contributionForm");
  const locale = useLocale();
  const [type, setType] = useState<ContributionType>(defaultType);
  const [email, setEmail] = useState("");
  const [title, setTitle] = useState("");
  const [details, setDetails] = useState("");
  const [status, setStatus] = useState<"idle" | "success" | "error">("idle");
  const [isSubmitting, setIsSubmitting] = useState(false);

  async function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setIsSubmitting(true);
    setStatus("idle");

    try {
      await submitPortalContribution(
        {
          type,
          email,
          payload: { title, details },
        },
        locale,
      );
      setStatus("success");
      setEmail("");
      setTitle("");
      setDetails("");
      onSuccess?.();
    } catch {
      setStatus("error");
    } finally {
      setIsSubmitting(false);
    }
  }

  const fieldClassName =
    "w-full rounded-xl border border-[#00709E33] bg-white px-4 py-3 text-sm text-secondary outline-none transition-colors placeholder:text-muted-foreground/60 focus:border-primary";

  return (
    <form
      onSubmit={handleSubmit}
      dir={isRtl ? "rtl" : "ltr"}
      className="space-y-4 rounded-2xl border border-[#00709E33] bg-[#f4fafc] p-5 sm:p-6"
    >
      <div className="space-y-2">
        <label htmlFor="contribution-type" className="text-sm font-medium text-secondary">
          {t("typeLabel")}
        </label>
        <select
          id="contribution-type"
          value={type}
          onChange={(event) => setType(event.target.value as ContributionType)}
          className={fieldClassName}
        >
          <option value="publications">{t("types.publications")}</option>
          <option value="cities">{t("types.cities")}</option>
          <option value="organizations">{t("types.organizations")}</option>
        </select>
      </div>

      <div className="space-y-2">
        <label htmlFor="contribution-email" className="text-sm font-medium text-secondary">
          {t("emailLabel")}
        </label>
        <input
          id="contribution-email"
          type="email"
          required
          value={email}
          onChange={(event) => setEmail(event.target.value)}
          placeholder={t("emailPlaceholder")}
          className={fieldClassName}
        />
      </div>

      <div className="space-y-2">
        <label htmlFor="contribution-title" className="text-sm font-medium text-secondary">
          {t("titleLabel")}
        </label>
        <input
          id="contribution-title"
          type="text"
          required
          value={title}
          onChange={(event) => setTitle(event.target.value)}
          placeholder={t("titlePlaceholder")}
          className={fieldClassName}
        />
      </div>

      <div className="space-y-2">
        <label htmlFor="contribution-details" className="text-sm font-medium text-secondary">
          {t("detailsLabel")}
        </label>
        <textarea
          id="contribution-details"
          required
          rows={4}
          value={details}
          onChange={(event) => setDetails(event.target.value)}
          placeholder={t("detailsPlaceholder")}
          className={cn(fieldClassName, "resize-none")}
        />
      </div>

      {status === "success" ? (
        <p className="text-sm font-medium text-primary">{t("success")}</p>
      ) : null}
      {status === "error" ? (
        <p className="text-sm font-medium text-destructive">{t("error")}</p>
      ) : null}

      <Button
        type="submit"
        disabled={isSubmitting}
        className="w-full rounded-xl bg-primary hover:bg-primary/90 sm:w-auto"
      >
        {t("submit")}
        <Send className={cn("size-4", isRtl && "rotate-180")} aria-hidden />
      </Button>
    </form>
  );
}
