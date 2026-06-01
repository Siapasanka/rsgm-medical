@if(in_array($role, ['superadmin', 'admin']))
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card label="Total Pasien" :value="$cards['total_pasien']" tone="blue" />
        <x-stat-card label="Kunjungan Hari Ini" :value="$cards['kunjungan_hari_ini']" tone="emerald" />
        <x-stat-card label="Antrian Menunggu" :value="$cards['menunggu']" tone="amber" />
        <x-stat-card label="Rekam Medis Hari Ini" :value="$cards['rekam_medis_hari_ini']" tone="violet" />
    </div>
@elseif($role === 'dokter')
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card label="Pendaftaran Hari Ini" :value="$cards['kunjungan_hari_ini']" tone="blue" />
        <x-stat-card label="Menunggu" :value="$cards['menunggu']" tone="amber" />
        <x-stat-card label="Diperiksa" :value="$cards['diperiksa']" tone="violet" />
        <x-stat-card label="Selesai" :value="$cards['selesai']" tone="emerald" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-stat-card label="Pasien Menunggu Hari Ini" :value="$cards['menunggu']" tone="amber" />
        <x-stat-card label="Selesai Hari Ini" :value="$cards['selesai']" tone="emerald" />
        <x-stat-card label="Rekam Medis Saya Hari Ini" :value="$myRecordsToday->count()" tone="blue" />
    </div>
@endif
