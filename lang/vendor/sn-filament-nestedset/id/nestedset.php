<?php

return [
    'tree' => [
        'empty_label' => 'Tidak ada data',
    ],

    'action' => [
        'create_child_node' => 'Tambah Sub-standar',

        'delete_failed_title' => 'Gagal menghapus',
        'delete_failed_body_has_child' => 'Node masih memiliki sub-standar. Hapus sub-standar terlebih dahulu.',

        'move_node' => 'Pindahkan node',
        'move_node_success' => 'Node berhasil dipindahkan',
        'move_node_failed' => 'Gagal memindahkan node',
        'move_node_failed_body_depth' => 'Gagal memindahkan node, level target tidak boleh melebihi level maksimum :level.',

        'fix_nestedset' => 'Perbaiki Tree',
        'fix_nestedset_success' => 'Tree berhasil diperbaiki',
        'fix_nestedset_failed' => 'Gagal memperbaiki Tree',
        'fix_nestedset_failed_body' => 'Gagal memperbaiki Tree, silakan coba lagi.',
        'fix_nestedset_failed_body_depth' => 'Gagal memperbaiki Tree, level target tidak boleh melebihi level maksimum :level.',
        'fix_nestedset_failed_body_circular' => 'Gagal memperbaiki Tree, node tidak boleh memiliki dirinya sendiri sebagai induk.',
        'fix_nestedset_failed_body_root' => 'Gagal memperbaiki Tree, node tidak boleh memiliki dirinya sendiri sebagai induk.',
    ],

    'field' => [
        'parent_select_field' => 'Induk Standar',
        'parent_select_field_placeholder' => 'Pilih induk standar',
        'parent_select_field_empty_label' => 'Tidak ada induk standar',
    ],
];
