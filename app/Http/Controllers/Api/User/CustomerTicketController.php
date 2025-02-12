<?php
namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Models\supportTicket;
use App\Models\supportTicketConv;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CustomerTicketController extends Controller
{
    public function SupportTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject'     => 'required',
            'type'        => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $attachments = [];
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $path          = $file->store('support-ticket', 'public');
                $attachments[] = $path;
            }
        }

        $ticketData = [
            'user_id'     => $request->user()->id,
            'subject'     => $request->subject,
            'type'        => $request->type,
            'priority'    => $request->priority ?? 'normal',
            'description' => $request->description,
            'attachment'  => json_encode($attachments),
            'status'      => 'open',
        ];

        try {
            $ticket = self::createSupportTicket($ticketData);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => 'Something went wrong. Please try again.',

                ],
            ], 500);
        }

        return response()->json([
            'message' => 'Support ticket created successfully.',
            'ticket'  => $ticket,
        ], 200);
    }

/**
 * Create a support ticket.
 *
 * @param array $data
 * @return supportTicket
 */
    public static function createSupportTicket(array $data)
    {
        return supportTicket::create([
            'user_id'     => $data['user_id'],
            'subject'     => $data['subject'],
            'type'        => $data['type'],
            'priority'    => $data['priority'],
            'description' => $data['description'],
            'attachment'  => $data['attachment'],
            'status'      => $data['status'],
        ]);
    }
    public function get_support_tickets(Request $request)
    {
        return response()->json(supportTicket::where('user_id', $request->user()->id)->latest()->get(), 200);
    }
    

    public function reply_support_ticket(Request $request, $ticket_id)
    {
        DB::table('support_tickets')->where(['id' => $ticket_id])->update([
            'status'     => 'open',
            'updated_at' => now(),
        ]);
        $attachments = [];
        if ($request->hasFile('attachment')) {
            
            foreach ($request->file('attachment') as $file) {
                $path = $file->store('support-ticket', 'public');
                $attachments[] = $path;
            }
        }

        $support                    = new supportTicketConv();
        $support->support_ticket_id = $ticket_id;
        $support->attachment        = json_encode($attachments);
        $support->admin_id          = 0;
        $support->customer_message  = $request['customer_message'];
        $support->save();
        return response()->json(['message' => 'Support ticket reply sent.'], 200);
    }

    public function get_support_ticket_conv($ticket_id)
    {
        $conversations  = supportTicketConv::where('support_ticket_id', $ticket_id)->get();
        $support_ticket = supportTicket::find($ticket_id);

        $conversations = $conversations->toArray();

        if ($support_ticket) {
            $description = [
                'support_ticket_id'   => $ticket_id,
                'admin_id'            => null,
                'customer_message'    => $support_ticket->description,
                'admin_message'       => null,
                'attachment'          => $support_ticket->attachment,
                'attachment_full_url' => $support_ticket->attachment_full_url,
                'position'            => 0,
                'created_at'          => $support_ticket->created_at,
                'updated_at'          => $support_ticket->updated_at,
            ];
            array_unshift($conversations, $description);
        }
        return response()->json($conversations, 200);
    }
    public function support_ticket_close($id)
    {
        $ticket = supportTicket::find($id);
        if ($ticket) {
            $ticket->status     = 0;
            $ticket->updated_at = now();
            $ticket->save();
            return response()->json(['message' => 'Successfully close the ticket'], 200);
        }
        return response()->json(['message' => 'Ticket not found'], 403);
    }

}
