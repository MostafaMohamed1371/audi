import { ContactFormSection } from "@/app/components/contact/contact-form-section";
import { ContactInfoSection } from "@/app/components/contact/contact-info-section";
import { MembershipFormSection } from "@/app/components/contact/membership-form-section";
import { fetchContactInfo } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

export async function ContactContent() {
  const t = await getTranslations("contact");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const apiInfo = await fetchContactInfo(locale);

  const fallbackItems = t.raw("info.items") as {
    label: string;
    value: string;
    type: "phone" | "fax" | "email";
    href?: string;
  }[];

  const contactItems = (apiInfo?.items ?? fallbackItems) as typeof fallbackItems;

  return (
    <>
      <ContactInfoSection
        title={apiInfo?.title ?? t("info.title")}
        subtitle={apiInfo?.subtitle ?? t("info.subtitle")}
        addressLabel={apiInfo?.addressLabel ?? t("info.addressLabel")}
        address={apiInfo?.address ?? t("info.address")}
        mapTitle={apiInfo?.mapTitle ?? t("info.mapTitle")}
        mapEmbedUrl={apiInfo?.mapEmbedUrl}
        items={contactItems}
        isRtl={isRtl}
      />
      <MembershipFormSection isRtl={isRtl} />
      <ContactFormSection isRtl={isRtl} />
    </>
  );
}
