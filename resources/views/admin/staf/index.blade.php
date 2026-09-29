<x-dashboard-layout :title="'Kelola Akun Admin'">
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.staf.create') }}" class="rounded-full bg-olive-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
            + Tambah Akun Admin
        </a>
    </div>

    <div class="rounded-2xl border border-olive-100 bg-white p-5">
        @if ($staff->isEmpty())
            <p class="text-sm text-olive-600 py-6 text-center">Belum ada akun admin.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-olive-100 text-left text-xs uppercase tracking-wide text-olive-500">
                            <th class="py-2 pr-4">Nama</th>
                            <th class="py-2 pr-4">Email</th>
                            <th class="py-2 pr-4">No. WhatsApp</th>
                            <th class="py-2 pr-4">Terdaftar</th>
                            <th class="py-2 pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staff as $admin)
                            <tr class="border-b border-olive-50">
                                <td class="py-3 pr-4 font-medium text-olive-900">
                                    {{ $admin->nama }}
                                    @if ($admin->id === auth()->id())
                                        <span class="ml-1 text-xs text-olive-500">(Anda)</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-olive-700">{{ $admin->email }}</td>
                                <td class="py-3 pr-4 text-olive-700">{{ $admin->no_telepon }}</td>
                                <td class="py-3 pr-4 text-olive-700">{{ $admin->created_at->translatedFormat('d M Y') }}</td>
                                <td class="py-3 pr-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.staf.edit', $admin) }}" class="rounded-full border border-olive-300 px-3 py-1.5 text-xs font-medium text-olive-800 hover:bg-cream-100">Ubah</a>
                                        @if ($admin->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.staf.destroy', $admin) }}" onsubmit="return confirm('Hapus akun admin ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-full px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $staff->links() }}</div>
        @endif
    </div>
</x-dashboard-layout>
