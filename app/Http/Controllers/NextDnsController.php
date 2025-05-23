<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\NextDnsService;

class NextDnsController extends Controller
{
    protected $nextDns;

    public function __construct(NextDnsService $nextDns)
    {
        $this->nextDns = $nextDns;
    }

    public function showDenylist()
    {
        $denylist = $this->nextDns->getDenylist();
        return view('nextdns.denylist', ['denylist' => $denylist]);
    }
    
    public function deleteDenylist($id)
    {
        $success = $this->nextDns->deleteFromDenylist($id);

        return redirect()->back()->with(
            $success ? 'success' : 'error',
            $success ? "Berhasil menghapus {$id}." : "Gagal menghapus {$id}."
        );
    }
    
public function toggleActive(Request $request)
{
    \Log::info('toggleActive called', $request->all());

    $domainId = $request->input('id');
    $newStatus = $request->input('active');

    $result = $this->nextDns->updateDenylistActiveStatus($domainId, $newStatus);
    return response()->json($result);
}

}
