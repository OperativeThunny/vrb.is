FROM python:3.12-alpine

RUN mkdir -p /app/tt \
    && apk add --no-cache curl zsh \
    && pip install --upgrade pip
COPY ./static /app/tt/static
COPY ./templates /app/tt/templates
COPY *.py /app/tt
COPY *.txt /app/tt
WORKDIR /app/tt

RUN pip install --upgrade -r requirements.txt

WORKDIR /app

EXPOSE 8080

#CMD ["python", "-m", "flask", "-A", "tt", "run", "--host", "0.0.0.0", "--port", "8080", "--debug", "--with-threads", "--reload"]
#env SCRIPT_NAME=/tt gunicorn -b 0.0.0.0:8080 --reload tt
ENV SCRIPT_NAME=/tt
CMD ["python", "-m", "gunicorn", "-b", "0.0.0.0:8080", "--reload", "tt"]