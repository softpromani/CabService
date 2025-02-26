<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\supportTicket;
use App\Models\supportTicketConv;
use Illuminate\Http\Request;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        $query = SupportTicket::orderBy('id', 'desc');

        // Search by subject
        if ($request->has('searchValue') && ! empty($request->searchValue)) {
            $search = $request->searchValue;
            $query->where('subject', 'LIKE', "%{$search}%");
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $tickets = $query->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.support-ticket.ticket', compact('tickets'))->render(),
            ]);
        }

        return view('admin.support-ticket.ticket', compact('tickets'));
    }

    public function updateStatus(Request $request)
    {
        $ticket = supportTicket::find($request->id);
        if ($ticket) {
            $ticket->status = $request->status;
            $ticket->save();

            return response()->json([
                'success' => true,
                'status'  => $ticket->status,
                'message' => 'Ticket status updated successfully!',
            ], 200);
        }

        return response()->json(['success' => false, 'message' => 'Ticket not found'], 404);
    }

    public function reply(Request $request)
    {

        if ($request->attachment == null && $request->replay == null) {
            toastr()->error('Type something!');
            return back();
        }
        $attachments = [];
        if ($request->hasFile('attachment')) {

            foreach ($request->file('attachment') as $file) {
                $path          = $file->store('filesystems.default', 'public');
                $attachments[] = $path;
            }
        }

        supportTicketConv::create([
            'admin_message'     => $request->replay,
            'admin_id'          => $request->adminId,
            'support_ticket_id' => $request->id,
            'attachment'        => json_encode($attachments),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return back();
    }
    public function getView($id)
    {
        $supportTicket = SupportTicket::with('conversations', 'customer')->where('id', $id)->get();

        if (! $supportTicket) {
            return redirect()->back()->with('error', 'Support Ticket not found.');
        }

        return view('admin.support-ticket.ticket-view', compact('supportTicket'));
    }

}
