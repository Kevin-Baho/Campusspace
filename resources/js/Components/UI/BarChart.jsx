export default function BarChart({ data, label = 'd', value = 'total' }) {
    if (!data?.length) return <p className="text-sm text-gray-500">Belum ada data.</p>;
    const max = Math.max(...data.map((d) => Number(d[value])), 1);
    return (
        <div className="flex h-40 items-end gap-1.5" role="img" aria-label="Grafik tren booking">
            {data.map((d, i) => (
                <div key={i} className="flex flex-1 flex-col items-center justify-end gap-1">
                    <span className="text-[10px] text-gray-500">{d[value]}</span>
                    <div className="w-full rounded-t bg-gradient-to-t from-blue-600 to-violet-500" style={{ height: `${(Number(d[value]) / max) * 100}%`, minHeight: 4 }} />
                    <span className="text-[10px] text-gray-500">{String(d[label]).slice(5)}</span>
                </div>
            ))}
        </div>
    );
}
