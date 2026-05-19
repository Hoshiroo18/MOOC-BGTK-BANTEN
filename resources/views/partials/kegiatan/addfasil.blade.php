{{--
  Partial: partials/kegiatan/addfasil.blade.php
  Include: @include('partials.kegiatan.addfasil', ['fasilitators' => $fasilitators])
  Opsional edit: ['fasilitators' => $fasilitators, 'selectedIds' => [...]]
--}}

<div class="form-group fasil-field-group" id="fasilFieldGroup">
  <label>Fasilitator <span class="required-mark">*</span></label>

  <div class="fasil-selected-tags" id="fasilSelectedTags">
    <span class="fasil-tag-placeholder" id="fasilTagPlaceholder">
      Belum ada fasilitator dipilih
    </span>
  </div>

  <button type="button" class="fasil-open-modal-btn" id="fasilOpenModalBtn"
    aria-haspopup="dialog" aria-controls="fasilModal">
    <span class="fasil-btn-icon">＋</span>
    Pilih Fasilitator
  </button>

  <small class="form-help">Pilih satu atau lebih fasilitator untuk kegiatan ini.</small>

  <div id="fasilHiddenInputs"></div>
</div>

{{-- ── MODAL ─────────────────────────────────────────────────────────────────── --}}
<div class="fasil-modal" id="fasilModal" hidden role="dialog" aria-modal="true" aria-labelledby="fasilModalTitle">
  <div class="fasil-modal-backdrop" id="fasilModalBackdrop"></div>

  <div class="fasil-modal-card">

    <div class="fasil-modal-header">
      <div>
        <span class="fasil-modal-kicker">Database Fasilitator</span>
        <h3 id="fasilModalTitle">Pilih Fasilitator</h3>
      </div>
      <button type="button" class="fasil-modal-close" id="fasilModalClose" aria-label="Tutup modal">✕</button>
    </div>

    {{-- Search: nama atau NIP --}}
    <div class="fasil-modal-search">
      <span class="fasil-search-icon">⌕</span>
      <input type="text" id="fasilSearchInput"
        placeholder="Cari nama atau NIP fasilitator..."
        autocomplete="off" aria-label="Cari fasilitator">
      <button type="button" id="fasilSearchReset" class="fasil-search-reset" title="Reset">✕</button>
    </div>

    <div class="fasil-modal-body">
      <div class="fasil-modal-list" id="fasilModalList">
        @forelse($fasilitators as $fasil)
          <label
            class="fasil-modal-item"
            data-nama="{{ strtolower($fasil->nama) }}"
            data-nip="{{ $fasil->nip }}"
          >
            <input
              type="checkbox"
              class="fasil-modal-checkbox"
              data-id="{{ $fasil->fasilitator_id }}"
              data-nama="{{ $fasil->nama }}"
              value="{{ $fasil->fasilitator_id }}"
              {{ isset($selectedIds) && in_array($fasil->fasilitator_id, (array) $selectedIds) ? 'checked' : '' }}
            >

            <span class="fasil-modal-avatar">
              {{ strtoupper(substr($fasil->nama, 0, 1)) }}
            </span>

            <div class="fasil-modal-info">
              <span class="fasil-modal-name">{{ $fasil->nama }}</span>
              <span class="fasil-modal-nip">{{ $fasil->nip }}</span>
              <div class="fasil-modal-meta">
                @if($fasil->pangkat)
                  <span class="fasil-badge">{{ $fasil->pangkat }}</span>
                @endif
                @if($fasil->jabatan)
                  <span class="fasil-badge">{{ $fasil->jabatan }}</span>
                @endif
                @if($fasil->jenis_jabatan)
                  <span class="fasil-badge fasil-badge-secondary">{{ $fasil->jenis_jabatan }}</span>
                @endif
                @if($fasil->status_kepegawaian)
                  <span class="fasil-badge fasil-badge-green">{{ $fasil->status_kepegawaian }}</span>
                @endif
              </div>
            </div>

            <span class="fasil-modal-check-icon">✓</span>
          </label>
        @empty
          <div class="fasil-modal-empty">Belum ada data fasilitator.</div>
        @endforelse
      </div>

      <div class="fasil-modal-empty-search" id="fasilModalEmptySearch" hidden>
        Fasilitator tidak ditemukan.
      </div>
    </div>

    <div class="fasil-modal-footer">
      <span class="fasil-selected-count" id="fasilSelectedCount">0 dipilih</span>
      <div class="fasil-modal-footer-actions">
        <button type="button" class="fasil-btn-cancel" id="fasilModalCancel">Batal</button>
        <button type="button" class="fasil-btn-confirm" id="fasilModalConfirm">Konfirmasi Pilihan</button>
      </div>
    </div>

  </div>
</div>

{{-- ── STYLES ───────────────────────────────────────────────────────────────── --}}
<style>
.fasil-field-group { position: relative; }
.fasil-selected-tags {
  display: flex; flex-wrap: wrap; gap: 6px; min-height: 38px;
  padding: 6px 10px; border: 1.5px solid var(--border, #d1d5db);
  border-radius: 8px; background: var(--input-bg, #f9fafb);
  margin-bottom: 8px; align-items: center;
}
.fasil-tag-placeholder { color: var(--muted, #9ca3af); font-size: .85rem; }
.fasil-tag {
  display: inline-flex; align-items: center; gap: 5px;
  background: var(--primary, #2563eb); color: #fff;
  font-size: .78rem; font-weight: 600; padding: 3px 10px 3px 9px;
  border-radius: 20px; letter-spacing: .01em;
}
.fasil-tag-remove {
  cursor: pointer; font-size: .9rem; opacity: .75; transition: opacity .15s;
  padding: 0 2px; background: none; border: none; color: inherit;
}
.fasil-tag-remove:hover { opacity: 1; }
.fasil-open-modal-btn {
  display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px;
  border-radius: 8px; border: 1.5px dashed var(--primary, #2563eb);
  background: transparent; color: var(--primary, #2563eb);
  font-size: .88rem; font-weight: 600; cursor: pointer; transition: background .18s, color .18s;
}
.fasil-open-modal-btn:hover { background: var(--primary, #2563eb); color: #fff; }
.fasil-btn-icon { font-size: 1rem; }

.fasil-modal {
  position: fixed; inset: 0; z-index: 9999;
  display: flex; align-items: center; justify-content: center;
}
.fasil-modal[hidden] { display: none; }
.fasil-modal-backdrop {
  position: absolute; inset: 0;
  background: rgba(0,0,0,.45); backdrop-filter: blur(3px);
}
.fasil-modal-card {
  position: relative; z-index: 1; background: var(--card-bg, #fff);
  border-radius: 16px; width: min(560px, 95vw); max-height: 85vh;
  display: flex; flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,.22); overflow: hidden;
  animation: fasilSlideIn .22s ease;
}
@keyframes fasilSlideIn {
  from { transform: translateY(22px) scale(.97); opacity: 0; }
  to   { transform: none; opacity: 1; }
}
.fasil-modal-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  padding: 20px 22px 14px; border-bottom: 1px solid var(--border, #e5e7eb);
}
.fasil-modal-kicker {
  display: block; font-size: .72rem; font-weight: 700;
  letter-spacing: .08em; text-transform: uppercase;
  color: var(--primary, #2563eb); margin-bottom: 2px;
}
.fasil-modal-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--heading, #111827); }
.fasil-modal-close {
  background: none; border: none; font-size: 1.05rem; cursor: pointer;
  color: var(--muted, #6b7280); padding: 4px 6px; border-radius: 6px;
  transition: background .15s, color .15s;
}
.fasil-modal-close:hover { background: #fee2e2; color: #dc2626; }

.fasil-modal-search {
  position: relative; padding: 12px 22px 10px;
  border-bottom: 1px solid var(--border, #e5e7eb);
}
.fasil-search-icon {
  position: absolute; left: 36px; top: 50%; transform: translateY(-50%);
  font-size: 1.1rem; color: var(--muted, #9ca3af); pointer-events: none;
}
#fasilSearchInput {
  width: 100%; padding: 9px 36px 9px 34px;
  border: 1.5px solid var(--border, #d1d5db); border-radius: 8px;
  font-size: .9rem; background: var(--input-bg, #f9fafb);
  color: var(--text, #111827); outline: none; transition: border-color .18s; box-sizing: border-box;
}
#fasilSearchInput:focus { border-color: var(--primary, #2563eb); }
.fasil-search-reset {
  position: absolute; right: 32px; top: 50%; transform: translateY(-50%);
  background: none; border: none; font-size: .8rem; color: var(--muted, #9ca3af);
  cursor: pointer; padding: 4px; border-radius: 4px; transition: color .15s;
}
.fasil-search-reset:hover { color: #dc2626; }

.fasil-modal-body { flex: 1; overflow-y: auto; padding: 6px 0; }
.fasil-modal-list { display: flex; flex-direction: column; }

.fasil-modal-item {
  display: flex; align-items: flex-start; gap: 12px; padding: 12px 22px;
  cursor: pointer; transition: background .12s; user-select: none; position: relative;
}
.fasil-modal-item:hover { background: var(--hover-bg, #f3f4f6); }
.fasil-modal-item.is-checked { background: var(--primary-light, #eff6ff); }
.fasil-modal-checkbox { position: absolute; opacity: 0; pointer-events: none; }

.fasil-modal-avatar {
  width: 40px; height: 40px; border-radius: 50%; background: var(--primary, #2563eb);
  color: #fff; font-weight: 700; font-size: 1rem;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; transition: background .15s; margin-top: 2px;
}
.fasil-modal-item.is-checked .fasil-modal-avatar { background: #16a34a; }

.fasil-modal-info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.fasil-modal-name { font-size: .92rem; font-weight: 600; color: var(--text, #111827); }
.fasil-modal-nip  { font-size: .78rem; color: var(--muted, #6b7280); font-family: monospace; }
.fasil-modal-meta { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px; }

.fasil-badge {
  display: inline-block; font-size: .7rem; font-weight: 600;
  padding: 2px 8px; border-radius: 20px;
  background: #e0e7ff; color: #3730a3;
}
.fasil-badge-secondary { background: #f3f4f6; color: #374151; }
.fasil-badge-green     { background: #dcfce7; color: #166534; }

.fasil-modal-check-icon {
  font-size: 1rem; font-weight: 700; color: #16a34a;
  opacity: 0; transition: opacity .15s; margin-top: 2px; flex-shrink: 0;
}
.fasil-modal-item.is-checked .fasil-modal-check-icon { opacity: 1; }

.fasil-modal-empty, .fasil-modal-empty-search {
  padding: 32px; text-align: center; color: var(--muted, #6b7280); font-size: .9rem;
}
.fasil-modal-footer {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 22px; border-top: 1px solid var(--border, #e5e7eb); gap: 12px;
}
.fasil-selected-count { font-size: .85rem; font-weight: 600; color: var(--primary, #2563eb); min-width: 70px; }
.fasil-modal-footer-actions { display: flex; gap: 10px; }
.fasil-btn-cancel {
  padding: 8px 18px; border-radius: 8px; border: 1.5px solid var(--border, #d1d5db);
  background: transparent; color: var(--text, #374151);
  font-size: .88rem; font-weight: 600; cursor: pointer; transition: background .15s;
}
.fasil-btn-cancel:hover { background: #f3f4f6; }
.fasil-btn-confirm {
  padding: 8px 20px; border-radius: 8px; border: none;
  background: var(--primary, #2563eb); color: #fff;
  font-size: .88rem; font-weight: 700; cursor: pointer; transition: background .18s, transform .1s;
}
.fasil-btn-confirm:hover { background: #1d4ed8; }
.fasil-btn-confirm:active { transform: scale(.97); }
</style>

{{-- ── SCRIPT ───────────────────────────────────────────────────────────────── --}}
<script>
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {

    const modal          = document.getElementById('fasilModal');
    const backdrop       = document.getElementById('fasilModalBackdrop');
    const openBtn        = document.getElementById('fasilOpenModalBtn');
    const closeBtn       = document.getElementById('fasilModalClose');
    const cancelBtn      = document.getElementById('fasilModalCancel');
    const confirmBtn     = document.getElementById('fasilModalConfirm');
    const searchInput    = document.getElementById('fasilSearchInput');
    const searchReset    = document.getElementById('fasilSearchReset');
    const selectedTags   = document.getElementById('fasilSelectedTags');
    const tagPlaceholder = document.getElementById('fasilTagPlaceholder');
    const hiddenInputs   = document.getElementById('fasilHiddenInputs');
    const selectedCount  = document.getElementById('fasilSelectedCount');
    const emptySearch    = document.getElementById('fasilModalEmptySearch');
    const checkboxes     = document.querySelectorAll('.fasil-modal-checkbox');

    if (!modal || !openBtn) return;

    let snapshot = {};

    function getChecked() {
      const result = {};
      checkboxes.forEach(function (cb) {
        if (cb.checked) result[cb.dataset.id] = cb.dataset.nama;
      });
      return result;
    }

    function syncItemVisual(cb) {
      const item = cb.closest('.fasil-modal-item');
      if (!item) return;
      cb.checked ? item.classList.add('is-checked') : item.classList.remove('is-checked');
    }

    function updateCount() {
      const n = document.querySelectorAll('.fasil-modal-checkbox:checked').length;
      if (selectedCount) selectedCount.textContent = n + ' dipilih';
    }

    function renderTags(selected) {
      if (!selectedTags || !hiddenInputs) return;
      selectedTags.innerHTML = '';
      hiddenInputs.innerHTML = '';
      const ids = Object.keys(selected);
      if (ids.length === 0) {
        if (tagPlaceholder) {
          const ph = tagPlaceholder.cloneNode(true);
          ph.hidden = false;
          selectedTags.appendChild(ph);
        }
        return;
      }
      ids.forEach(function (id) {
        const tag      = document.createElement('span');
        tag.className  = 'fasil-tag';
        tag.dataset.id = id;
        tag.innerHTML  =
          '<span>' + selected[id] + '</span>' +
          '<button type="button" class="fasil-tag-remove" data-id="' + id + '">✕</button>';
        selectedTags.appendChild(tag);

        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'fasilitator_ids[]';
        inp.value = id;
        hiddenInputs.appendChild(inp);
      });

      selectedTags.querySelectorAll('.fasil-tag-remove').forEach(function (btn) {
        btn.addEventListener('click', function () {
          const tid = btn.dataset.id;
          checkboxes.forEach(function (cb) {
            if (String(cb.dataset.id) === String(tid)) {
              cb.checked = false;
              syncItemVisual(cb);
            }
          });
          delete selected[tid];
          renderTags(selected);
          updateCount();
        });
      });
    }

    function openModal() {
      snapshot = getChecked();
      modal.hidden = false;
      document.body.classList.add('modal-open');
      updateCount();
      if (searchInput) {
        searchInput.value = '';
        filterList('');
        setTimeout(function () { searchInput.focus(); }, 80);
      }
    }

    function closeModal() {
      modal.hidden = true;
      document.body.classList.remove('modal-open');
    }

    function cancelModal() {
      checkboxes.forEach(function (cb) {
        cb.checked = !!snapshot[cb.dataset.id];
        syncItemVisual(cb);
      });
      updateCount();
      closeModal();
    }

    function confirmModal() {
      renderTags(getChecked());
      closeModal();
    }

    function filterList(keyword) {
      const kw = keyword.toLowerCase().trim();
      let visible = 0;
      document.querySelectorAll('.fasil-modal-item').forEach(function (item) {
        // cari di nama DAN nip
        const nama = item.dataset.nama || '';
        const nip  = item.dataset.nip  || '';
        if (kw === '' || nama.includes(kw) || nip.includes(kw)) {
          item.style.display = '';
          visible++;
        } else {
          item.style.display = 'none';
        }
      });
      if (emptySearch) emptySearch.hidden = kw === '' || visible > 0;
    }

    // Init
    checkboxes.forEach(function (cb) { syncItemVisual(cb); });
    renderTags(getChecked());
    updateCount();

    if (openBtn)    openBtn.addEventListener('click', openModal);
    if (closeBtn)   closeBtn.addEventListener('click', cancelModal);
    if (cancelBtn)  cancelBtn.addEventListener('click', cancelModal);
    if (confirmBtn) confirmBtn.addEventListener('click', confirmModal);
    if (backdrop)   backdrop.addEventListener('click', cancelModal);

    if (searchInput) {
      searchInput.addEventListener('input', function () { filterList(searchInput.value); });
    }
    if (searchReset) {
      searchReset.addEventListener('click', function () {
        if (searchInput) { searchInput.value = ''; filterList(''); searchInput.focus(); }
      });
    }

    checkboxes.forEach(function (cb) {
      cb.closest('.fasil-modal-item').addEventListener('click', function () {
        cb.checked = !cb.checked;
        syncItemVisual(cb);
        updateCount();
      });
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.hidden) cancelModal();
    });

  });
})();
</script>
