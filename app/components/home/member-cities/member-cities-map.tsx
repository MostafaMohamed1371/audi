"use client";

import { fetchMemberCitiesGeoJson, type GeoJsonFeatureCollection } from "@/lib/api";
import { useEffect, useRef } from "react";

type Props = {
  isRtl: boolean;
};

type FeatureCollection = GeoJsonFeatureCollection;

const COUNTRY_STYLE = {
  fillColor: "#00709e",
  fillOpacity: 0.38,
  color: "#ffffff",
  weight: 1.25,
};

const COUNTRY_HOVER_STYLE = {
  fillOpacity: 0.58,
  weight: 2,
  color: "#e8f4f8",
};

function clusterSize(count: number) {
  if (count >= 80) return 52;
  if (count >= 40) return 46;
  if (count >= 15) return 40;
  return 34;
}

const PIN_WIDTH = 28;
const PIN_HEIGHT = 42;

function createGooglePinSvg() {
  return `<svg class="member-cities-google-pin" xmlns="http://www.w3.org/2000/svg" width="${PIN_WIDTH}" height="${PIN_HEIGHT}" viewBox="0 0 28 42" aria-hidden="true">
    <path fill="#E25142" stroke="#ffffff" stroke-width="1.5" d="M14 1C7.925 1 3 5.925 3 12c0 8.82 11 28 11 28s11-19.18 11-28c0-6.075-4.925-11-11-11z"/>
    <circle fill="#ffffff" cx="14" cy="12" r="4.5"/>
  </svg>`;
}

function createGoogleClusterHtml(count: number, size: number) {
  return `<div class="member-cities-google-cluster" style="--cluster-size:${size}px"><span>${count}</span></div>`;
}

export function MemberCitiesMap({ isRtl }: Props) {
  const containerRef = useRef<HTMLDivElement>(null);
  const mapRef = useRef<import("leaflet").Map | null>(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container || mapRef.current) {
      return;
    }

    let disposed = false;

    async function initMap() {
      const L = (await import("leaflet")).default;
      await import("leaflet.markercluster");

      if (disposed || !containerRef.current) {
        return;
      }

      const map = L.map(containerRef.current, {
        center: [26, 32],
        zoom: 4,
        minZoom: 3,
        maxZoom: 8,
        scrollWheelZoom: false,
        attributionControl: false,
        zoomControl: false,
      });

      mapRef.current = map;

      L.control.zoom({ position: "topleft" }).addTo(map);

      L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
        { maxZoom: 19 },
      ).addTo(map);

      const markers = L.markerClusterGroup({
        showCoverageOnHover: false,
        spiderLegPolylineOptions: { opacity: 0 },
        maxClusterRadius: 56,
        iconCreateFunction(cluster) {
          const count = cluster.getChildCount();
          const size = clusterSize(count);

          return L.divIcon({
            html: createGoogleClusterHtml(count, size),
            className: "member-cities-cluster-wrap",
            iconSize: L.point(size, size),
            iconAnchor: [size / 2, size / 2],
          });
        },
      });

      const locale = isRtl ? "ar" : "en";
      const apiData = await fetchMemberCitiesGeoJson(locale);

      const [countriesRes, citiesRes] = apiData
        ? [null, null]
        : await Promise.all([
            fetch("/data/arab-countries.geojson"),
            fetch("/data/member-cities.geojson"),
          ]);

      const countries = apiData
        ? apiData.countries
        : ((await countriesRes!.json()) as FeatureCollection);
      const cities = apiData
        ? apiData.cities
        : ((await citiesRes!.json()) as FeatureCollection);

      if (disposed) {
        return;
      }

      const countriesLayer = L.geoJSON(countries, {
        style: () => ({ ...COUNTRY_STYLE }),
        onEachFeature(_feature, layer) {
          layer.on({
            mouseover: (event) => {
              const target = event.target;
              target.setStyle(COUNTRY_HOVER_STYLE);
              target.bringToFront();
            },
            mouseout: (event) => {
              countriesLayer.resetStyle(event.target);
            },
          });
        },
      }).addTo(map);

      L.geoJSON(cities, {
        pointToLayer(feature, latlng) {
          const name =
            typeof feature.properties?.Name === "string"
              ? feature.properties.Name
              : "";

          return L.marker(latlng, {
            icon: L.divIcon({
              className: "member-cities-marker-wrap",
              html: createGooglePinSvg(),
              iconSize: [PIN_WIDTH, PIN_HEIGHT],
              iconAnchor: [PIN_WIDTH / 2, PIN_HEIGHT],
            }),
          }).bindPopup(
            `<div dir="${isRtl ? "rtl" : "ltr"}" class="member-cities-popup"><span class="member-cities-popup-dot"></span>${name}</div>`,
          );
        },
      }).addTo(markers);

      map.addLayer(markers);
    }

    void initMap();

    return () => {
      disposed = true;
      mapRef.current?.remove();
      mapRef.current = null;
    };
  }, [isRtl]);

  return (
    <div className="member-cities-map-shell overflow-hidden rounded-2xl border border-[#00709e]/15 bg-linear-to-b from-[#f4f9fc] to-white p-1 shadow-[0_20px_50px_rgba(17,31,66,0.08)] sm:rounded-[28px]">
      <div
        ref={containerRef}
        className="member-cities-map h-[min(50vh,420px)] w-full overflow-hidden rounded-xl bg-[#eef4f8] sm:h-[min(60vh,480px)] sm:rounded-[24px] lg:h-[min(70vh,560px)]"
      />
    </div>
  );
}
