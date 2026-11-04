<?php
declare(strict_types=1);

namespace Core;

abstract class Controller
{
    public function __construct(protected Request $request)
    {
    }

    /** Full page. */
    protected function page(string $template, array $data = [], string $layout = 'layouts/main'): Response
    {
        return Response::html(View::render($template, $data, $layout));
    }

    /**
     * A form that normally opens in a modal. AJAX request -> bare HTML for the modal;
     * direct visit -> the same form inside a normal page.
     */
    protected function modal(string $template, array $data = []): Response
    {
        if ($this->request->isAjax()) {
            return Response::html(View::render($template, $data, null));
        }
        return $this->page($template, $data + ['asPage' => true]);
    }

    protected function json(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $path): Response
    {
        return Response::redirect($path);
    }

    /**
     * Success reply for forms and buttons. With reload/redirect the message is
     * flashed so the next page shows it as a toast; otherwise the browser toasts it now.
     */
    protected function success(string $message, array $extra = []): Response
    {
        if (isset($extra['reload']) || isset($extra['redirect'])) {
            Session::flash('success', $message);
        }
        return Response::json(['ok' => true, 'message' => $message] + $extra);
    }

    protected function fail(string $message, array $errors = [], int $status = 422): Response
    {
        return Response::json(['ok' => false, 'message' => $message, 'errors' => $errors], $status);
    }

    protected function abort(int $status): never
    {
        throw new HttpException($status);
    }

    /** Validate or stop with a 422 the browser shows inside the modal. */
    protected function validate(array $rules, array $labels = []): array
    {
        $errors = Validator::check($this->request->input, $rules, $labels);
        if ($errors) {
            $this->failValidation($errors);
        }
        return $this->request->input;
    }

    protected function failValidation(array $errors): never
    {
        Response::json(['ok' => false, 'message' => 'Please fix the highlighted fields.', 'errors' => $errors], 422)->send();
        exit;
    }
}
