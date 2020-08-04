<?php

namespace App\Http\Controllers\Api;

/**
 * Base Controllers
 */
use App\Http\Controllers\Controller;

/**
 * Requests
 */
use Illuminate\Http\Request;
use App\Http\Requests\EditServerRequest;

/**
 * Models
 */
use App\Models\Server;
use App\Models\User;

class ServersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servers = Server::all();
        return response()->success('success', array('servers' => $servers ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\EditServerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EditServerRequest $request)
    {
        $data = $request->validated();

        /** @var \App\Model\User $user */
        $user = auth('api')->user();

        if (empty($data['id'])) {
            // new server
            $server         = new Server();

        } else {
            // update server
            $server         = Server::where('id', $data['id'])->get()[0];
        }

        $server->host       = $data['host'];
        $server->port       = $data['port'];
        $server->username   = $data['username'];
        if (!empty($data['password'])) {
            $server->password   = $data['password'];
        }
        $server->active     = $data['active'];
        $server->user_id    = $user->id;
        $server->save();

        return response()->success( 'success', array( 'server' => $server ) );
    }

    /**
     * Activate a server - deactive any active server
     *
     * @param  \App\Http\Requests\EditServerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request)
    {
        $id           = $request->get('id', false);
        $servers      = Server::all();
        $updateServer = null;
        foreach ($servers as $server) {
            if ($server->active == 1 && $server->id != $id) {
                /** @var \App\Model\Server $update */
                $update         = Server::where('id', $server->id )->get();
                $update->active = 0;
                $update->save();
            }
            if ($server->id == $id) {
                /** @var \App\Model\User $updateServer */
                $updateServer = $server;
            }
        }
        $updateServer->active = 1;
        $updateServer->save();
        return response()->success( 'success', array( 'server' => $updateServer ) );
    }

    /**
     * @todo We are handling updates with the store method
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Server $server)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function destroy(Server $server, $id)
    {
        $result = 'failed';
        if ($id) {
            $result = Server::where('id', $id)->delete();
        }
        return response()->success( 'success', array( 'deleted' => $result ) );
    }
}
