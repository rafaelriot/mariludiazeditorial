<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubscriberModel;

class Subscribers extends BaseController
{
    protected SubscriberModel $subscriberModel;

    public function __construct()
    {
        $this->subscriberModel = model(SubscriberModel::class);
    }

    public function index()
    {
        $subscribers = $this->subscriberModel->orderBy('created_at', 'DESC')->paginate(30);

        return view('admin/subscribers/index', [
            'subscribers' => $subscribers,
            'pager'       => $this->subscriberModel->pager,
            'total'       => $this->subscriberModel->countAllResults(false),
            'pageTitle'   => 'Suscriptores del Newsletter',
        ]);
    }

    /**
     * Exporta los suscriptores a un archivo CSV para Mailchimp, Brevo, Substack o Excel
     */
    public function export()
    {
        $subscribers = $this->subscriberModel->where('status', 'active')->orderBy('created_at', 'DESC')->findAll();

        $filename = 'suscriptores-marilu-diaz-' . date('Ymd-His') . '.csv';

        $output = "Email,Estado,Origen,Fecha_Registro\n";
        foreach ($subscribers as $s) {
            $output .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\"\n",
                str_replace('"', '""', $s->email),
                $s->status,
                $s->source,
                $s->created_at ? $s->created_at->toDateTimeString() : ''
            );
        }

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody("\xEF\xBB\xBF" . $output); // UTF-8 BOM para compatibilidad con Excel
    }

    public function delete(int $id)
    {
        $this->subscriberModel->delete($id);
        return redirect()->back()->with('message', 'Suscriptor eliminado.');
    }
}
