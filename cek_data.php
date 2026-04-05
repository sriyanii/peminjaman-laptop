$data = DB::table('peminjaman')->select('id', 'user_id', 'laptop_id', 'status', 'tanggal_kembali', 'tanggal_kembali_rencana', 'created_at')->orderBy('id', 'desc')->limit(20)->get();
foreach($data as $row) { echo "ID: $row->id, Status: $row->status, Tgl Kembali: " . ($row->tanggal_kembali ?? 'NULL') . ", Tgl Rencana: $row->tanggal_kembali_rencana\n"; }


