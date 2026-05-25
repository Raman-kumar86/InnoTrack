@extends('layouts.landing')

@section('content')
<section class="relative bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 pt-20 pb-24 overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(#6366f1_0.5px,transparent_1px)] [background-size:50px_50px] opacity-10"></div>

    <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-12 gap-16 items-center">
            
            <!-- Left Side -->
            <div class="lg:col-span-6">
                <div class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 shadow-sm border">
                    <span class="text-emerald-600">🇮🇳</span>
                    <span class="font-medium text-sm text-slate-700">A Government of India Initiative</span>
                </div>

                <h1 class="mt-8 text-5xl lg:text-6xl font-bold tracking-tighter leading-none text-slate-900">
                    Monitor India’s Startup Ecosystem with <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-600">Real-Time Intelligence</span>
                </h1>

                <p class="mt-6 text-lg text-slate-600 max-w-lg">
                    Comprehensive analytics platform for tracking startups, funding rounds, DPIIT recognition, and state-wise growth — built for governance and policy making.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="/auth/login" 
                       class="inline-flex items-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-3xl font-semibold text-lg transition shadow-lg">
                        Explore Dashboard →
                    </a>
                    <a href="{{ route('search.index') }}" 
                       class="inline-flex items-center gap-3 border-2 border-slate-300 hover:border-slate-400 px-8 py-4 rounded-3xl font-semibold text-lg transition">
                        View Public Analytics
                    </a>
                </div>

                <!-- Stats -->
                <div class="mt-16 grid grid-cols-2 sm:grid-cols-4 gap-6">
                    <div>
                        <div class="text-4xl font-bold text-slate-900">100K+</div>
                        <div class="text-sm text-slate-500">Startups Tracked</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-slate-900">₹50K Cr+</div>
                        <div class="text-sm text-slate-500">Funding Monitored</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-slate-900">36</div>
                        <div class="text-sm text-slate-500">States &amp; UTs</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-slate-900">25+</div>
                        <div class="text-sm text-slate-500">Sectors</div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Dashboard Mock -->
            <div class="lg:col-span-6 relative">
                <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden">
                    <!-- Dashboard Top Bar -->
                    <div class="bg-slate-900 text-white px-6 py-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-500 rounded-2xl flex items-center justify-center">🚀</div>
                            <div>
                                <p class="font-semibold">Dashboard Overview</p>
                                <p class="text-xs text-slate-400">Welcome back, DPIIT Admin</p>
                            </div>
                        </div>
                        <div class="text-xs bg-slate-800 px-4 py-2 rounded-2xl">FY 2024-25</div>
                    </div>

                    <!-- KPI Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-slate-100">
                        <div class="bg-white p-5">
                            <p class="text-xs text-slate-500">Total Startups</p>
                            <p class="text-3xl font-bold mt-2">1,23,456</p>
                            <p class="text-emerald-600 text-sm mt-1">↑ 12.5%</p>
                        </div>
                        <div class="bg-white p-5">
                            <p class="text-xs text-slate-500">Total Funding</p>
                            <p class="text-3xl font-bold mt-2">₹50,245 Cr</p>
                            <p class="text-emerald-600 text-sm mt-1">↑ 18.7%</p>
                        </div>
                        <div class="bg-white p-5">
                            <p class="text-xs text-slate-500">DPIIT Recognized</p>
                            <p class="text-3xl font-bold mt-2">82,456</p>
                            <p class="text-emerald-600 text-sm mt-1">↑ 16.3%</p>
                        </div>
                        <div class="bg-white p-5">
                            <p class="text-xs text-slate-500">Active States</p>
                            <p class="text-3xl font-bold mt-2">36/36</p>
                            <p class="text-emerald-600 text-sm mt-1">100% Coverage</p>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                            <div class="md:col-span-3 bg-slate-50 rounded-2xl p-6">
                                <p class="font-medium mb-4">Funding Trend</p>
                                <canvas id="fundingChart" class="h-56"></canvas>
                            </div>
                            <div class="md:col-span-2 bg-slate-50 rounded-2xl p-6">
                                <p class="font-medium mb-4">Sector Distribution</p>
                                <canvas id="sectorChart" class="h-56"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Card -->
                <div class="absolute -top-6 -right-6 bg-white p-6 rounded-3xl shadow-xl border w-60">
                    <p class="text-sm text-slate-500">Funding Growth This Month</p>
                    <p class="text-4xl font-bold text-indigo-600 mt-2">₹8,456 Cr</p>
                    <p class="text-emerald-500 text-sm font-medium">↑ 20.1%</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-indigo-600 uppercase tracking-widest text-sm font-semibold">Powerful Features</span>
            <h2 class="text-4xl font-bold mt-3">Everything You Need to Monitor &amp; Accelerate Startups</h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $features = [
                ['icon'=>'📊','title'=>'Real-time Analytics','desc'=>'Live metrics and alerts'],
                ['icon'=>'🗺️','title'=>'State-wise Monitoring','desc'=>'Compare states & UTs'],
                ['icon'=>'💰','title'=>'Funding Intelligence','desc'=>'Investor and deal tracking'],
                ['icon'=>'📈','title'=>'Growth Tracking','desc'=>'Startup momentum analysis'],
                ['icon'=>'🏅','title'=>'DPIIT Recognition','desc'=>'Approval status tracking'],
                ['icon'=>'🤖','title'=>'AI Insights','desc'=>'Anomaly detection & suggestions'],
                ['icon'=>'🔐','title'=>'Role-based Access','desc'=>'Secure government access'],
                ['icon'=>'📋','title'=>'Automated Reports','desc'=>'Compliance ready exports'],
            ];
            @endphp

            @foreach($features as $f)
            <div class="p-8 border border-slate-100 rounded-3xl hover:border-indigo-200 hover:shadow transition">
                <div class="text-5xl mb-6">{{ $f['icon'] }}</div>
                <h3 class="font-semibold text-xl">{{ $f['title'] }}</h3>
                <p class="text-slate-600 mt-3">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('fundingChart'), {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep'],
            datasets: [{ label: 'Funding ₹ Cr', data: [2800,3500,4200,3900,5100,6800,7500,9200,11800], borderColor: '#4f46e5', tension: 0.4, borderWidth: 3 }]
        },
        options: { responsive: true, plugins: { legend: { display: false }}}
    });

    new Chart(document.getElementById('sectorChart'), {
        type: 'doughnut',
        data: {
            labels: ['Tech','Healthcare','Fintech','Education','Others'],
            datasets: [{ data: [42,19,15,11,13], backgroundColor: ['#4f46e5','#22d3ee','#a78bfa','#34d399','#64748b'] }]
        },
        options: { responsive: true, cutout: '70%' }
    });
</script>
@endpush