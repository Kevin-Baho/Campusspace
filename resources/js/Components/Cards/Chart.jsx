/** Bar chart SVG sederhana tanpa dependensi. data: [{label, value}] */
export default function BarChart({ data, height = 160 }) {
  if (!data.length) return <p className="text-sm text-slate-500">Belum ada data.</p>;
  const max = Math.max(...data.map((d) => d.value), 1);
  const w = 100 / data.length;
  return (
    <svg viewBox={`0 0 100 ${height / 2}`} className="w-full" role="img" aria-label="Grafik">
      {data.map((d, i) => {
        const h = (d.value / max) * (height / 2 - 14);
        return (
          <g key={i}>
            <rect x={i * w + w * 0.15} y={height / 2 - 10 - h} width={w * 0.7} height={h} rx="1" className="fill-violet-500" />
            <text x={i * w + w / 2} y={height / 2 - 2} fontSize="3" textAnchor="middle" className="fill-slate-500">{d.label}</text>
            <text x={i * w + w / 2} y={height / 2 - 12 - h} fontSize="3" textAnchor="middle" className="fill-slate-700 dark:fill-slate-200">{d.value}</text>
          </g>
        );
      })}
    </svg>
  );
}
